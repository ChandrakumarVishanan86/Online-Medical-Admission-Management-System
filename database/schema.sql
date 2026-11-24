CREATE DATABASE IF NOT EXISTS medadmit CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE medadmit;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS status_logs, application_documents, applications, programs, applicant_profiles, users;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL, role ENUM('applicant','officer','admin') NOT NULL DEFAULT 'applicant', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB;
CREATE TABLE applicant_profiles(user_id INT PRIMARY KEY, dob DATE NULL, gender ENUM('male','female','other') NULL, phone VARCHAR(20) NULL, address VARCHAR(255) NULL,
  nic VARCHAR(15) NULL, exam_year SMALLINT NULL, bio_marks DECIMAL(5,2) NULL, chem_marks DECIMAL(5,2) NULL, phys_marks DECIMAL(5,2) NULL,
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE) ENGINE=InnoDB;
CREATE TABLE programs(id INT AUTO_INCREMENT PRIMARY KEY, code VARCHAR(10) NOT NULL UNIQUE, name VARCHAR(150) NOT NULL, description TEXT,
  seats INT NOT NULL, min_avg DECIMAL(5,2) NOT NULL, min_each DECIMAL(5,2) NOT NULL, deadline DATE NOT NULL, is_open TINYINT(1) NOT NULL DEFAULT 1) ENGINE=InnoDB;
CREATE TABLE applications(id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, program_id INT NOT NULL, ref_no VARCHAR(12) NULL UNIQUE,
  status ENUM('draft','submitted','under_review','shortlisted','interview','accepted','rejected') NOT NULL DEFAULT 'draft',
  merit_score DECIMAL(5,2) NULL, submitted_at DATETIME NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_program(user_id,program_id), KEY idx_status(status), KEY idx_prog_status(program_id,status,merit_score),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE, FOREIGN KEY(program_id) REFERENCES programs(id)) ENGINE=InnoDB;
CREATE TABLE application_documents(id INT AUTO_INCREMENT PRIMARY KEY, application_id INT NOT NULL, doc_type VARCHAR(30) NOT NULL,
  original_name VARCHAR(200) NOT NULL, stored_name VARCHAR(64) NOT NULL, verified TINYINT(1) NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_app(application_id), FOREIGN KEY(application_id) REFERENCES applications(id) ON DELETE CASCADE) ENGINE=InnoDB;
CREATE TABLE status_logs(id INT AUTO_INCREMENT PRIMARY KEY, application_id INT NOT NULL, old_status VARCHAR(20) NULL, new_status VARCHAR(20) NOT NULL,
  note VARCHAR(250) NULL, changed_by INT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, KEY idx_app(application_id),
  FOREIGN KEY(application_id) REFERENCES applications(id) ON DELETE CASCADE, FOREIGN KEY(changed_by) REFERENCES users(id) ON DELETE SET NULL) ENGINE=InnoDB;
INSERT INTO programs(code,name,description,seats,min_avg,min_each,deadline,is_open) VALUES
('MBBS','Bachelor of Medicine & Bachelor of Surgery','Five-year medical degree with clinical rotations.',5,75,65,'2027-03-31',1),
('BSCN','BSc Nursing','Four-year nursing degree with hospital placements.',8,60,50,'2027-03-31',1),
('BPHM','BSc Pharmacy','Four-year pharmacy degree.',6,65,55,'2027-02-28',1),
('BMLS','BSc Medical Laboratory Sciences','Allied health program in diagnostics.',6,55,45,'2027-02-28',1);
