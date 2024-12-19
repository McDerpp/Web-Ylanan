-- Create schemas (databases) if not already created
CREATE SCHEMA IF NOT EXISTS `usjr` DEFAULT CHARACTER SET utf8;
CREATE SCHEMA IF NOT EXISTS `usjr-jsp1b40` DEFAULT CHARACTER SET utf8;

-- Switch to the `usjr-jsp1b40` schema
USE `usjr-jsp1b40`;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `programs`;
DROP TABLE IF EXISTS `departments`;
DROP TABLE IF EXISTS `colleges`;

-- Create `colleges` table
CREATE TABLE `colleges` (
  `collid` INT NOT NULL,
  `collfullname` VARCHAR(100) NOT NULL,
  `collshortname` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`collid`),
  UNIQUE KEY `idx_collfullname` (`collfullname`)
);

-- Create `departments` table
CREATE TABLE `departments` (
  `deptid` INT NOT NULL,
  `deptfullname` VARCHAR(100) NOT NULL,
  `deptshortname` VARCHAR(20),
  `deptcollid` INT NOT NULL, 
  PRIMARY KEY (`deptid`),
  CONSTRAINT `fk_department_college_id`
     FOREIGN KEY (`deptcollid`) 
     REFERENCES `colleges` (`collid`)
     ON DELETE NO ACTION
     ON UPDATE NO ACTION
);

-- Create `programs` table
CREATE TABLE `programs` (
  `progid` INT NOT NULL,
  `progfullname` VARCHAR(100) NOT NULL,
  `progshortname` VARCHAR(20),
  `progcollid` INT NOT NULL,
  `progcolldeptid` INT NOT NULL,
  PRIMARY KEY (`progid`),
  UNIQUE KEY `idx_progfullname` (`progfullname`),
  CONSTRAINT `fk_program_college_id`
     FOREIGN KEY (`progcollid`)
     REFERENCES `colleges` (`collid`)
     ON DELETE NO ACTION
     ON UPDATE NO ACTION,
  CONSTRAINT `fk_program_college_department_id`
     FOREIGN KEY (`progcolldeptid`)
     REFERENCES `departments` (`deptid`)
     ON DELETE NO ACTION
     ON UPDATE NO ACTION
);

-- Create `students` table
CREATE TABLE `students` (
  `student_id` VARCHAR(50) NOT NULL,
  `first_name` VARCHAR(50) NOT NULL,
  `middle_name` VARCHAR(50),
  `last_name` VARCHAR(50) NOT NULL,
  `college` VARCHAR(100) NOT NULL,
  `program` VARCHAR(100) NOT NULL,
  `year` INT NOT NULL,
  PRIMARY KEY (`student_id`),
  CONSTRAINT `fk_student_college_id`
     FOREIGN KEY (`college`) 
     REFERENCES `colleges` (`collfullname`)
     ON DELETE NO ACTION
     ON UPDATE NO ACTION,
  CONSTRAINT `fk_student_program_id`
     FOREIGN KEY (`program`) 
     REFERENCES `programs` (`progfullname`)
     ON DELETE NO ACTION
     ON UPDATE NO ACTION
);

-- Create `student_entries` table
CREATE TABLE `student_entries` (
  `entry_id` INT NOT NULL AUTO_INCREMENT,
  `student_id` VARCHAR(50) NOT NULL,
  `first_name` VARCHAR(50) NOT NULL,
  `middle_name` VARCHAR(50),
  `last_name` VARCHAR(50) NOT NULL,
  `college` VARCHAR(100) NOT NULL,
  `program` VARCHAR(100) NOT NULL,
  `year` INT NOT NULL,
  PRIMARY KEY (`entry_id`)
);

-- Create `users` table
CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
);

-- Insert sample data into `colleges`
INSERT INTO `colleges` VALUES (1,'School of Business and Management','SBM');
INSERT INTO `colleges` VALUES (2,'School of Arts and Sciences','SAS');
INSERT INTO `colleges` VALUES (3,'School of Engineering','SoENG');
INSERT INTO `colleges` VALUES (4,'School of Education','SED');
INSERT INTO `colleges` VALUES (5,'School of Computer Studies','SCS');
INSERT INTO `colleges` VALUES (6,'School of Allied Medical Sciences','SAMS');

-- Insert sample data into `departments`
INSERT INTO `departments` VALUES(1001,'Accountancy and Finance Department',NULL,1);
INSERT INTO `departments` VALUES(1002,'Business and Entrepreneurship Department',NULL,1);
INSERT INTO `departments` VALUES(1003,'Marketing and Human Resource Management Department',NULL,1);
INSERT INTO `departments` VALUES(1004,'Tourism and Hospitality Management Department','THMD',1);
INSERT INTO `departments` VALUES(2001,'Department of Communications, Language and Literature','DLL',2);
INSERT INTO `departments` VALUES(2002,'Department of Mathematics and Sciences','DMS',2);
INSERT INTO `departments` VALUES(2003,'Department of Social Sciences and Philosophy','DSSP',2);
INSERT INTO `departments` VALUES(2004,'Department of Psychology and Library Information Science','DPLIS',2);
INSERT INTO `departments` VALUES(3001,'Department of Civil Engineering',NULL,3);
INSERT INTO `departments` VALUES(3002,'Department of Computer Engineering',NULL,3);
INSERT INTO `departments` VALUES(3003,'Department of Electronics and Communications Engineering',NULL,3);
INSERT INTO `departments` VALUES(3004,'Department of Electrical Engineering',NULL,3);
INSERT INTO `departments` VALUES(3005,'Department of Industrial Engineering',NULL,3);
INSERT INTO `departments` VALUES(3006,'Department of Mechanical Engineering',NULL,3);
INSERT INTO `departments` VALUES(4001,'Department of Teacher Education',NULL,4);
INSERT INTO `departments` VALUES(4002,'Department of Physical Education',NULL,4);
INSERT INTO `departments` VALUES(4003,'Department of Special Education',NULL,4);
INSERT INTO `departments` VALUES(5001,'CS/IT Department',NULL,5);
INSERT INTO `departments` VALUES(6001,'Department of Nursing',NULL,6);

-- Insert sample data into `programs`
INSERT INTO `programs` VALUES(1001,'Bachelor of Science in Accountancy','BSA',1,1001);
INSERT INTO `programs` VALUES(1002,'Bachelor of Science in Management Accounting','BSMA',1,1001);
INSERT INTO `programs` VALUES(1003,'Bachelor of Science in Business Administration Major in Operation Management','BSBA-OM',1,1003);
INSERT INTO `programs` VALUES(1004,'Bachelor of Science in Business Administration Major in Human Resource Development Management','BSBA-HRDM',1,1003);
INSERT INTO `programs` VALUES(1005,'Bachelor of Science in Business Administration Major in Marketing Management','BSBA-MM',1,1003);
INSERT INTO `programs` VALUES(1006,'Bachelor of Science in Business Administration Major in Financial Management','BSBA-FM',1,1001);
INSERT INTO `programs` VALUES(1007,'Bachelor of Science in Entrepreneurship','BS-Entrepreneurship',1,1002);
INSERT INTO `programs` VALUES(1008,'Bachelor of Science in Hospitality Management','BSHM',1,1004);
INSERT INTO `programs` VALUES(1009,'Bachelor of Science in Hospitality Management Major in Food and Beverage','BSHM-FB',1,1004);
INSERT INTO `programs` VALUES(1010,'Associate in Hospitality Management','AHM',1,1004);
INSERT INTO `programs` VALUES(1011,'Associate in Tourism','ATourism',1,1004);
INSERT INTO `programs` VALUES(2001,'Bachelor of Arts in Communication','ABComm',2,2001);
INSERT INTO `programs` VALUES(2002,'Bachelor of Arts in English Language Studies','ABELS',2,2001);
INSERT INTO `programs` VALUES(2003,'Bachelor of Arts in Journalism','ABJournalism',2,2001);
INSERT INTO `programs` VALUES(2004,'Bachelor of Arts in Marketing Communication','ABMarComm',2,2001);
INSERT INTO `programs` VALUES(2005,'Bachelor of Science in Biology Major in Medical Biology','BSBio-MB',2,2002);
INSERT INTO `programs` VALUES(2006,'Bachelor of Science in Biology Major in Microbiology','BSBio-Microbio',2,2002);
INSERT INTO `programs` VALUES(2007,'Bachelor of Arts in Political Science','ABPolSci',2,2003);
INSERT INTO `programs` VALUES(2008,'Bachelor of Arts in International Studies','ABIS',2,2003);
INSERT INTO `programs` VALUES(2009,'Bachelor of Arts in Philosophy','ABPhilo',2,2003);
INSERT INTO `programs` VALUES(2010,'Bachelor of Science in Psychology','BSPsych',2,2004);
INSERT INTO `programs` VALUES(3001,'Bachelor of Science in Civil Engineering','BSCE',3,3001);
INSERT INTO `programs` VALUES(3002,'Bachelor of Science in Computer Engineering','BSCpE',3,3002);
INSERT INTO `programs` VALUES(3003,'Bachelor of Science in Electronics and Communications Engineering','BSECE',3,3003);
INSERT INTO `programs` VALUES(3004,'Bachelor of Science in Electrical Engineering','BSEE',3,3004);
INSERT INTO `programs` VALUES(3005,'Bachelor of Science in Industrial Engineering','BSIE',3,3005);
INSERT INTO `programs` VALUES(3006,'Bachelor of Science in Mechanical Engineering','BSME',3,3006);
INSERT INTO `programs` VALUES(4001,'Bachelor of Elementary Education','BEEd',4,4001);
INSERT INTO `programs` VALUES(4002,'Bachelor of Secondary Education','BSEd',4,4001);
INSERT INTO `programs` VALUES(4003,'Bachelor of Physical Education','BPEd',4,4002);
INSERT INTO `programs` VALUES(4004,'Bachelor of Special Education','BSPEd',4,4003);
INSERT INTO `programs` VALUES(5001,'Bachelor of Science in Computer Science','BSCS',5,5001);
INSERT INTO `programs` VALUES(5002,'Bachelor of Science in Information Technology','BSIT',5,5001);
INSERT INTO `programs` VALUES(5003,'Bachelor of Science in Information Systems','BSIS',5,5001);
INSERT INTO `programs` VALUES(6001,'Bachelor of Science in Nursing','BSN',6,6001);

-- Insert sample user into `users`
INSERT INTO `users` (username, password) VALUES ('admin', 'password123');
