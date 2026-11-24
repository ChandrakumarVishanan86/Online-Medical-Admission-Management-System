<?php
class AdminController {
  private function guard(){ require_role(['admin','officer']); }
  function dashboard(){ $this->guard();
    $m=q("SELECT (SELECT COUNT(*) FROM users WHERE role='applicant') applicants,
      (SELECT COUNT(*) FROM applications WHERE status<>'draft') submitted,
      (SELECT COUNT(*) FROM applications WHERE status IN('submitted','under_review')) pending,
      (SELECT COUNT(*) FROM applications WHERE status IN('shortlisted','interview')) shortlisted,
      (SELECT COUNT(*) FROM applications WHERE status='accepted') accepted")->fetch();
    $progs=q("SELECT p.*,p.seats-(SELECT COUNT(*) FROM applications a WHERE a.program_id=p.id AND a.status='accepted') seats_left FROM programs p ORDER BY p.name")->fetchAll();
    view('admin/dashboard',compact('m','progs')); }
  function applications(){ $this->guard(); $w=["a.status<>'draft'"]; $a=[];
    if(!empty($_GET['status'])){ $w[]='a.status=?'; $a[]=$_GET['status']; }
    if(!empty($_GET['program'])){ $w[]='a.program_id=?'; $a[]=(int)$_GET['program']; }
    if(!empty($_GET['q'])){ $w[]='(u.name LIKE ? OR u.email LIKE ? OR a.ref_no LIKE ?)'; $s='%'.$_GET['q'].'%'; array_push($a,$s,$s,$s); }
    $rows=q('SELECT a.*,u.name,u.email,p.code FROM applications a JOIN users u ON u.id=a.user_id JOIN programs p ON p.id=a.program_id WHERE '.implode(' AND ',$w).' ORDER BY a.merit_score DESC,a.id',$a)->fetchAll();
    $progs=q('SELECT id,code FROM programs ORDER BY code')->fetchAll(); view('admin/applications',compact('rows','progs')); }
  function review($id){ $this->guard();
    $a=q('SELECT a.*,u.name,u.email,p.name pname,p.code,p.seats FROM applications a JOIN users u ON u.id=a.user_id JOIN programs p ON p.id=a.program_id WHERE a.id=?',[$id])->fetch();
    if(!$a||$a['status']==='draft'){ http_response_code(404); exit('Not found'); }
    $pf=q('SELECT * FROM applicant_profiles WHERE user_id=?',[$a['user_id']])->fetch();
    $docs=q('SELECT * FROM application_documents WHERE application_id=?',[$id])->fetchAll();
    $logs=q('SELECT l.*,u.name by_name FROM status_logs l LEFT JOIN users u ON u.id=l.changed_by WHERE application_id=? ORDER BY l.id DESC',[$id])->fetchAll();
    view('admin/review',['a'=>$a,'pf'=>$pf,'docs'=>$docs,'logs'=>$logs,'next'=>TRANSITIONS[$a['status']]??[],'left'=>seats_left($a['program_id'])]); }
  function setStatus($id){ $this->guard(); $a=q('SELECT * FROM applications WHERE id=?',[$id])->fetch(); $new=$_POST['status']??'';
    if(!$a||!in_array($new,TRANSITIONS[$a['status']]??[])){ flash('err','Invalid status change.'); redirect("admin/applications/$id"); }
    if($new==='accepted'&&seats_left($a['program_id'])<1){ flash('err','No seats remaining in this program.'); redirect("admin/applications/$id"); }
    q('UPDATE applications SET status=? WHERE id=?',[$new,$id]); log_status($id,$a['status'],$new,substr(trim($_POST['note']??''),0,250),user()['id']);
    flash('ok','Status updated.'); redirect("admin/applications/$id"); }
  function verifyDoc($id){ $this->guard(); $d=q('SELECT * FROM application_documents WHERE id=?',[$id])->fetch(); if(!$d) redirect('admin');
    q('UPDATE application_documents SET verified=1-verified WHERE id=?',[$id]); redirect('admin/applications/'.$d['application_id']); }
  function download($id){ $this->guard(); $d=q('SELECT * FROM application_documents WHERE id=?',[$id])->fetch(); $f=$d?cfg('upload_dir').'/'.$d['stored_name']:'';
    if(!$d||!is_file($f)){ http_response_code(404); exit('Missing file'); }
    header('Content-Type: '.(new finfo(FILEINFO_MIME_TYPE))->file($f)); header('Content-Disposition: inline; filename="'.addslashes($d['original_name']).'"'); readfile($f); }
  function programs(){ $this->guard(); $edit=isset($_GET['edit'])?q('SELECT * FROM programs WHERE id=?',[(int)$_GET['edit']])->fetch():null;
    $rows=q("SELECT p.*,p.seats-(SELECT COUNT(*) FROM applications a WHERE a.program_id=p.id AND a.status='accepted') seats_left FROM programs p ORDER BY p.name")->fetchAll();
    view('admin/programs',['rows'=>$rows,'edit'=>$edit,'errors'=>[]]); }
  function saveProgram(){ require_role(['admin']); $d=array_map('trim',$_POST); $err=[];
    if(strlen($d['code']??'')<2||strlen($d['name']??'')<3) $err[]='Code and name are required.';
    if(!is_numeric($d['seats']??'')||$d['seats']<1) $err[]='Seats must be at least 1.';
    if(!is_numeric($d['min_avg']??'')||!is_numeric($d['min_each']??'')) $err[]='Eligibility marks must be numeric.';
    if(!strtotime($d['deadline']??'')) $err[]='Valid deadline required.';
    if($err){ $rows=q('SELECT *,seats seats_left FROM programs')->fetchAll(); return view('admin/programs',['rows'=>$rows,'edit'=>$d,'errors'=>$err]); }
    $v=[$d['code'],$d['name'],$d['description'],$d['seats'],$d['min_avg'],$d['min_each'],$d['deadline'],isset($_POST['is_open'])?1:0];
    if(!empty($d['id'])) q('UPDATE programs SET code=?,name=?,description=?,seats=?,min_avg=?,min_each=?,deadline=?,is_open=? WHERE id=?',array_merge($v,[(int)$d['id']]));
    else q('INSERT INTO programs(code,name,description,seats,min_avg,min_each,deadline,is_open) VALUES(?,?,?,?,?,?,?,?)',$v);
    flash('ok','Program saved.'); redirect('admin/programs'); }
  function allocate($pid){ $this->guard();
    $cap=seats_left($pid)-(int)q("SELECT COUNT(*) FROM applications WHERE program_id=? AND status IN('shortlisted','interview')",[$pid])->fetchColumn(); $n=0;
    if($cap>0) foreach(q("SELECT id FROM applications WHERE program_id=? AND status='under_review' ORDER BY merit_score DESC,submitted_at LIMIT $cap",[$pid])->fetchAll(PDO::FETCH_COLUMN) as $id){
      q("UPDATE applications SET status='shortlisted' WHERE id=?",[$id]); log_status($id,'under_review','shortlisted','Merit allocation',user()['id']); $n++; }
    flash('ok',"$n application(s) shortlisted by merit."); redirect('admin/programs'); }
}
