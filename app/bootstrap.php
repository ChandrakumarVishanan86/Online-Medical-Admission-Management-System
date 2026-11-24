<?php
session_start();
$GLOBALS['cfg'] = require __DIR__.'/config.php';
const TRANSITIONS = [
  'submitted'=>['under_review','rejected'], 'under_review'=>['shortlisted','rejected'],
  'shortlisted'=>['interview','accepted','rejected'], 'interview'=>['accepted','rejected'],
];
function cfg($k){ return $GLOBALS['cfg'][$k]; }
function db(): PDO { static $p; if(!$p){ $c=cfg('db');
  $p=new PDO("mysql:host={$c['host']};dbname={$c['name']};charset=utf8mb4",$c['user'],$c['pass'],
  [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]); } return $p; }
function q($sql,$args=[]){ $s=db()->prepare($sql); $s->execute($args); return $s; }
function e($s){ return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8'); }
function url($p=''){ return rtrim(cfg('base_url'),'/').'/'.ltrim($p,'/'); }
function redirect($p){ header('Location: '.url($p)); exit; }
function csrf(){ if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function csrf_field(){ return '<input type="hidden" name="_csrf" value="'.csrf().'">'; }
function csrf_check(){ if(!hash_equals($_SESSION['csrf']??'',$_POST['_csrf']??'')){ http_response_code(419); exit('Invalid CSRF token'); } }
function flash($k,$v=null){ if($v!==null){ $_SESSION['f'][$k]=$v; return; } $x=$_SESSION['f'][$k]??null; unset($_SESSION['f'][$k]); return $x; }
function user(){ return $_SESSION['user']??null; }
function is_staff(){ return in_array(user()['role']??'',['admin','officer']); }
function require_role(array $roles){ if(!user()) redirect('login'); if(!in_array(user()['role'],$roles)){ http_response_code(403); exit('Forbidden'); } }
function view($name,$data=[]){ extract($data); ob_start(); require __DIR__."/views/$name.php"; $content=ob_get_clean(); require __DIR__.'/views/layout.php'; }
function badge($s){ $c=['draft'=>'secondary','submitted'=>'primary','under_review'=>'info','shortlisted'=>'warning','interview'=>'dark','accepted'=>'success','rejected'=>'danger'][$s]??'light';
  return '<span class="badge text-bg-'.$c.'">'.e(ucwords(str_replace('_',' ',$s))).'</span>'; }
function merit($pf){ return round(($pf['bio_marks']+$pf['chem_marks']+$pf['phys_marks'])/3,2); }
function eligibility($pf,$pg){ $e=[];
  foreach(['dob','phone','address','nic','exam_year','bio_marks','chem_marks','phys_marks'] as $f) if($pf[$f]===null||$pf[$f]===''){ return ['Complete your profile first.']; }
  if(merit($pf)<$pg['min_avg']) $e[]='Average '.merit($pf).' is below the required '.$pg['min_avg'].'.';
  foreach(['bio'=>'Biology','chem'=>'Chemistry','phys'=>'Physics'] as $k=>$n) if($pf[$k.'_marks']<$pg['min_each']) $e[]="$n mark is below the minimum {$pg['min_each']}.";
  if($pg['deadline']<date('Y-m-d')) $e[]='Application deadline has passed.';
  return $e; }
function log_status($aid,$old,$new,$note,$by){ q('INSERT INTO status_logs(application_id,old_status,new_status,note,changed_by) VALUES(?,?,?,?,?)',[$aid,$old,$new,$note,$by]); }
function seats_left($pid){ return (int)q("SELECT p.seats-(SELECT COUNT(*) FROM applications a WHERE a.program_id=p.id AND a.status='accepted') FROM programs p WHERE p.id=?",[$pid])->fetchColumn(); }
