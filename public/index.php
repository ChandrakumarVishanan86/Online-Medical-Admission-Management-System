<?php
require __DIR__.'/../app/bootstrap.php';
foreach(['Auth','Applicant','Admin'] as $c) require __DIR__."/../app/Controllers/{$c}Controller.php";
$routes = [
 'GET /'=>['AuthController','home'], 'GET /login'=>['AuthController','loginForm'], 'POST /login'=>['AuthController','login'],
 'GET /register'=>['AuthController','registerForm'], 'POST /register'=>['AuthController','register'], 'POST /logout'=>['AuthController','logout'],
 'GET /programs'=>['ApplicantController','programs'], 'GET /profile'=>['ApplicantController','profile'], 'POST /profile'=>['ApplicantController','saveProfile'],
 'POST /apply/{id}'=>['ApplicantController','apply'], 'GET /applications'=>['ApplicantController','index'],
 'GET /applications/{id}'=>['ApplicantController','show'], 'POST /applications/{id}/upload'=>['ApplicantController','upload'],
 'POST /applications/{id}/submit'=>['ApplicantController','submit'], 'GET /applications/{id}/slip'=>['ApplicantController','slip'],
 'GET /admin'=>['AdminController','dashboard'], 'GET /admin/applications'=>['AdminController','applications'],
 'GET /admin/applications/{id}'=>['AdminController','review'], 'POST /admin/applications/{id}/status'=>['AdminController','setStatus'],
 'POST /admin/documents/{id}/verify'=>['AdminController','verifyDoc'], 'GET /admin/documents/{id}'=>['AdminController','download'],
 'GET /admin/programs'=>['AdminController','programs'], 'POST /admin/programs/save'=>['AdminController','saveProgram'],
 'POST /admin/programs/{id}/allocate'=>['AdminController','allocate'],
];
$method=$_SERVER['REQUEST_METHOD'];
$path='/'.trim(substr(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),strlen(rtrim(cfg('base_url'),'/'))),'/');
foreach($routes as $k=>$h){ [$m,$pat]=explode(' ',$k,2); if($m!==$method) continue;
  if(preg_match('#^'.str_replace('{id}','(\d+)',$pat).'$#',$path,$mm)){ array_shift($mm); if($method==='POST') csrf_check(); (new $h[0])->{$h[1]}(...array_map('intval',$mm)); exit; } }
http_response_code(404); view('404');
