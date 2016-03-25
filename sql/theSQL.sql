DROP TABLE IF EXISTS rates;

CREATE TABLE rate(
  rateId INT UNSIGNED NOT NULL AUTO_INCREMENT,
  rate VARCHAR(256) NOT NULL,
  ed VARCHAR(64) NOT NULL ,
  code VARCHAR(64) NOT NULL ,
  PRIMARY KEY (rateId),
  index(rate)

);

CREATE TABLE job(
  jobId INT UNSIGNED NOT NULL AUTO_INCREMENT,
  jobCode VARCHAR(128) NOT NULL,
  jobDescription VARCHAR(128) NOT NULL,
  PRIMARY KEY (jobId),
  INDEX(jobCode),
  INDEX(jobDescription)


);

INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Admin" , "Admin");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Admin ASB" , "Admin ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Admin BT" , "Admin BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "APS" , "Albuquerque Public Schools");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "APS ASB" , "Albuquerque Public Schools ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "ASB" , "ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "BC/BS ASB" , "Blue Cross Blue Shield ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "BC/BS BT" , "Blue Cross Blue Shield BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "BC/BS Centennial ASB" , "Blue Cross Blue Shield Centennial ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "BC/BS Centennial BT" , "Blue Cross Blue Shield Centennial BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "BT" , "BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Cigna Health ASB" , "Cigna Health Care ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Cigna Health BT" , "Cigna Health Care BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "DDW ASB" , "DDW ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "DDW BT" , "DDW BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "General ASB" , "General ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "General BT" , "General BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "MiVia ASB" , "MiVia ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "MiVia BT" , "MiVia BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Molina Centennial ASB" , "Molina Centennial ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Molina Centennial BT" , "Molina Centennail BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "NMMedicaid ASB" , "NM Medicaid ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "NMMedicaid BT" , "NM Medicaid BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Presby Centennial ASB" , "Presbyterian Centennial ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Presby Centennial BT" , "Presbyterian Centennial BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Presby Health ASB" , "Presbyterian Health Plan ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Presby Health BT" , "Presbyterian Health Plan BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Private Pay ASB" , "Private Pay ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Private Pay BT" , "Private Pay BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Sib Groups ASB" , "Sib Groups ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Sibs Groups BT" , "Sibs Groups BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "SSG" , "SSG");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Tricare/UMVS ASB" , "Tricare/UMVS ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "Tricare/UMVS BT" , "Tricare/UMVS BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "United BehHealth ASB" , "United Beh Health ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "United BehHealth BT" , "United Beh Health BT");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "United Centennial ASB" , "United Centennial ASB");
INSERT INTO job (jobId, jobCode, jobDescription) VALUES (null, "United Centennial BT" , "United Centennial BT");
