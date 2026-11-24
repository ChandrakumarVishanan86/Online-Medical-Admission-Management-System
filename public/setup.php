<?php // One-time seeding of demo users + applications. DELETE this file afterwards.
require __DIR__.'/../app/bootstrap.php';
if(q('SELECT COUNT(*) FROM users')->fetchColumn()>0) exit('Already seeded. Delete public/setup.php.');
$h=password_hash('Password@123',PASSWORD_DEFAULT);
foreach([['System Admin','admin@medadmit.test','admin'],['Admissions Officer','officer@medadmit.test','officer']] as $u) q('INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)',[$u[0],$u[1],$h,$u[2]]);
$demo=[['Nimali Perera',88,85,90,'submitted',1],['Kasun Silva',80,78,82,'under_review',1],['Ishara Fernando',70,72,69,'under_review',2],
       ['Tharindu Jayasuriya',92,90,94,'shortlisted',1],['Dilini Wickrama',66,60,64,'submitted',3],['Sahan Gunawardena',85,80,88,'draft',1]];
foreach($demo as $i=>$d){ $em=strtolower(explode(' ',$d[0])[0]).'@example.test';
  q("INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,'applicant')",[$d[0],$em,$h]); $uid=db()->lastInsertId();
  q('INSERT INTO applicant_profiles VALUES(?,?,?,?,?,?,?,?,?,?)',[$uid,'2004-05-1'.($i+1),$i%2?'male':'female','07712345'.$i,'Colombo, Sri Lanka','20040512345'.$i,2025,$d[1],$d[2],$d[3]]);
  q('INSERT INTO applications(user_id,program_id,status,merit_score,ref_no,submitted_at) VALUES(?,?,?,?,?,?)',[$uid,$d[5],$d[4],$d[4]==='draft'?null:round(($d[1]+$d[2]+$d[3])/3,2),$d[4]==='draft'?null:'MA26'.str_pad($i+1,5,'0',STR_PAD_LEFT),$d[4]==='draft'?null:date('Y-m-d H:i:s')]);
  log_status(db()->lastInsertId(),null,$d[4],'Seed data',$uid); }
echo 'Seeded. Logins: admin@medadmit.test / officer@medadmit.test / nimali@example.test — password Password@123. Now delete public/setup.php.';
