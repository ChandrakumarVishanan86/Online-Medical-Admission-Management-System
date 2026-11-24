<?php
class ApplicantController {
  const DOCS=['id_proof'=>'ID / Birth certificate','exam_results'=>'Exam results','photo'=>'Passport photo'];
  private function own($id){ require_role(['applicant']);
    $a=q('SELECT a.*,p.name pname,p.code,p.min_avg,p.min_each,p.deadline FROM applications a JOIN programs p ON p.id=a.program_id WHERE a.id=? AND a.user_id=?',[$id,user()['id']])->fetch();
    if(!$a){ http_response_code(404); exit('Not found'); } return $a; }
  function programs(){
    $rows=q("SELECT p.*, p.seats-(SELECT COUNT(*) FROM applications a WHERE a.program_id=p.id AND a.status='accepted') seats_left FROM programs p WHERE is_open=1 ORDER BY p.name")->fetchAll();
    $mine=user()?q('SELECT program_id,id FROM applications WHERE user_id=?',[user()['id']])->fetchAll(PDO::FETCH_KEY_PAIR):[];
    view('programs',compact('rows','mine'));
  }
  function profile(){ require_role(['applicant']); view('profile',['p'=>q('SELECT * FROM applicant_profiles WHERE user_id=?',[user()['id']])->fetch(),'errors'=>[]]); }
  function saveProfile(){ require_role(['applicant']); $d=[]; $err=[];
    foreach(['dob','gender','phone','address','nic','exam_year','bio_marks','chem_marks','phys_marks'] as $f) $d[$f]=trim($_POST[$f]??'');
    if(!strtotime($d['dob'])) $err[]='Valid date of birth required.';
    if(!in_array($d['gender'],['male','female','other'])) $err[]='Select a gender.';
    if(!preg_match('/^[0-9+\- ]{7,15}$/',$d['phone'])) $err[]='Valid phone number required.';
    if(!preg_match('/^([0-9]{9}[VvXx]|[0-9]{12})$/',$d['nic'])) $err[]='NIC must be 9 digits + V/X or 12 digits.';
    if($d['address']==='') $err[]='Address is required.';
    if(!ctype_digit($d['exam_year'])||$d['exam_year']<2000||$d['exam_year']>date('Y')) $err[]='Valid exam year required.';
    foreach(['bio_marks'=>'Biology','chem_marks'=>'Chemistry','phys_marks'=>'Physics'] as $f=>$n) if(!is_numeric($d[$f])||$d[$f]<0||$d[$f]>100) $err[]="$n marks must be 0-100.";
    if($err) return view('profile',['p'=>$d,'errors'=>$err]);
    q('UPDATE applicant_profiles SET dob=?,gender=?,phone=?,address=?,nic=?,exam_year=?,bio_marks=?,chem_marks=?,phys_marks=? WHERE user_id=?',array_merge(array_values($d),[user()['id']]));
    flash('ok','Profile saved.'); redirect('profile'); }
  function apply($pid){ require_role(['applicant']);
    if(!q('SELECT 1 FROM programs WHERE id=? AND is_open=1',[$pid])->fetch()){ flash('err','Program not available.'); redirect('programs'); }
    $x=q('SELECT id FROM applications WHERE user_id=? AND program_id=?',[user()['id'],$pid])->fetchColumn();
    if(!$x){ q("INSERT INTO applications(user_id,program_id,status) VALUES(?,?,'draft')",[user()['id'],$pid]); $x=db()->lastInsertId(); log_status($x,null,'draft','Draft created',user()['id']); }
    redirect("applications/$x"); }
  function index(){ require_role(['applicant']);
    $apps=q('SELECT a.*,p.name pname FROM applications a JOIN programs p ON p.id=a.program_id WHERE a.user_id=? ORDER BY a.id DESC',[user()['id']])->fetchAll(); view('applications',compact('apps')); }
  function show($id){ $a=$this->own($id);
    $docs=q('SELECT * FROM application_documents WHERE application_id=?',[$id])->fetchAll();
    $logs=q('SELECT * FROM status_logs WHERE application_id=? ORDER BY id DESC',[$id])->fetchAll();
    $pf=q('SELECT * FROM applicant_profiles WHERE user_id=?',[user()['id']])->fetch();
    $problems=$a['status']==='draft'?eligibility($pf,$a):[]; view('application',['a'=>$a,'docs'=>$docs,'logs'=>$logs,'problems'=>$problems,'types'=>self::DOCS]); }
  function upload($id){ $a=$this->own($id); $f=$_FILES['file']??null; $t=$_POST['doc_type']??'';
    $back="applications/$id";
    if($a['status']!=='draft'||!isset(self::DOCS[$t])){ flash('err','Upload not allowed.'); redirect($back); }
    $ext=strtolower(pathinfo($f['name']??'',PATHINFO_EXTENSION));
    if(!$f||$f['error']!==UPLOAD_ERR_OK||$f['size']>2*1048576||!in_array($ext,['pdf','jpg','jpeg','png'])
       ||!in_array((new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']),['application/pdf','image/jpeg','image/png'])){ flash('err','File must be PDF/JPG/PNG up to 2 MB.'); redirect($back); }
    $name=bin2hex(random_bytes(16)).".$ext"; @mkdir(cfg('upload_dir'),0775,true);
    move_uploaded_file($f['tmp_name'],cfg('upload_dir')."/$name");
    q('INSERT INTO application_documents(application_id,doc_type,original_name,stored_name) VALUES(?,?,?,?)',[$id,$t,substr($f['name'],0,200),$name]);
    flash('ok','Document uploaded.'); redirect($back); }
  function submit($id){ $a=$this->own($id); $pf=q('SELECT * FROM applicant_profiles WHERE user_id=?',[user()['id']])->fetch();
    $have=q('SELECT doc_type FROM application_documents WHERE application_id=?',[$id])->fetchAll(PDO::FETCH_COLUMN);
    if($a['status']!=='draft'||eligibility($pf,$a)||array_diff(['id_proof','exam_results'],$have)){ flash('err','Not eligible yet, or ID proof / exam results are missing.'); redirect("applications/$id"); }
    $ref='MA'.date('y').str_pad($id,5,'0',STR_PAD_LEFT);
    q("UPDATE applications SET status='submitted',merit_score=?,ref_no=?,submitted_at=NOW() WHERE id=?",[merit($pf),$ref,$id]);
    log_status($id,'draft','submitted','Submitted by applicant',user()['id']); flash('ok',"Submitted. Reference $ref"); redirect("applications/$id"); }
  function slip($id){ $a=$this->own($id); if($a['status']==='draft'){ redirect("applications/$id"); }
    $pf=q('SELECT u.name,u.email,p.* FROM applicant_profiles p JOIN users u ON u.id=p.user_id WHERE p.user_id=?',[user()['id']])->fetch();
    view('slip',compact('a','pf')); }
}
