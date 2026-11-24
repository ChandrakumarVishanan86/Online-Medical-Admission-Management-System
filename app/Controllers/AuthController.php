<?php
class AuthController {
  function home(){ if(!user()) redirect('programs'); redirect(is_staff()?'admin':'applications'); }
  function loginForm(){ view('login',['errors'=>[],'old'=>[]]); }
  function login(){
    $u=q('SELECT * FROM users WHERE email=?',[strtolower(trim($_POST['email']??''))])->fetch();
    if(!$u||!password_verify($_POST['password']??'',$u['password_hash'])) return view('login',['errors'=>['Invalid email or password.'],'old'=>$_POST]);
    session_regenerate_id(true); $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'role'=>$u['role']]; redirect('');
  }
  function registerForm(){ view('register',['errors'=>[],'old'=>[]]); }
  function register(){
    $n=trim($_POST['name']??''); $em=strtolower(trim($_POST['email']??'')); $p=$_POST['password']??''; $err=[];
    if(strlen($n)<3) $err[]='Full name is required.';
    if(!filter_var($em,FILTER_VALIDATE_EMAIL)) $err[]='A valid email is required.';
    if(strlen($p)<8) $err[]='Password must be at least 8 characters.';
    if($p!==($_POST['password2']??'')) $err[]='Passwords do not match.';
    if(!$err && q('SELECT 1 FROM users WHERE email=?',[$em])->fetch()) $err[]='That email is already registered.';
    if($err) return view('register',['errors'=>$err,'old'=>$_POST]);
    q('INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,"applicant")',[$n,$em,password_hash($p,PASSWORD_DEFAULT)]);
    $id=db()->lastInsertId(); q('INSERT INTO applicant_profiles(user_id) VALUES(?)',[$id]);
    session_regenerate_id(true); $_SESSION['user']=['id'=>$id,'name'=>$n,'role'=>'applicant'];
    flash('ok','Welcome! Please complete your profile before applying.'); redirect('profile');
  }
  function logout(){ $_SESSION=[]; session_destroy(); session_start(); redirect('login'); }
}
