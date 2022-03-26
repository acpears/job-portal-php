-- DROP TABLES
DROP TABLE IF EXISTS Outbound_Email;
DROP TABLE IF EXISTS Job_Application;
DROP TABLE IF EXISTS Job_Posting;
DROP TABLE IF EXISTS Admin;
DROP TABLE IF EXISTS Seeker;
DROP TABLE IF EXISTS Employer;
DROP TABLE IF EXISTS User_Category;
DROP TABLE IF EXISTS Security_Question;
DROP TABLE IF EXISTS Seeker_Plan;
DROP TABLE IF EXISTS Employer_Plan;
DROP TABLE IF EXISTS Registered_Payment_Method;
DROP TABLE IF EXISTS Bank_Account;
DROP TABLE IF EXISTS Credit_Card;
DROP TABLE IF EXISTS Transaction;
DROP TABLE IF EXISTS Wallet;
DROP TABLE IF EXISTS Payment_Method;
DROP TABLE IF EXISTS Job_Category;
-- CREATE TABLES
CREATE TABLE Outbound_Email (
  id INT NOT NULL AUTO_INCREMENT,
  sender VARCHAR(50) NOT NULL,
  receiver VARCHAR(50) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  body TEXT NOT NULL,
  date_sent DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);
CREATE TABLE Security_Question (
  id INT NOT NULL,
  question VARCHAR(255) NOT NULL,
  PRIMARY KEY(id)
);
CREATE TABLE Seeker_Plan (
  name VARCHAR(10) NOT NULL,
  monthly_cost INT NOT NULL,
  application_qty INT,
  description TEXT,
  PRIMARY KEY (name)
);
CREATE TABLE Employer_Plan (
  name VARCHAR(10) NOT NULL,
  monthly_cost INT NOT NULL,
  posting_qty INT,
  description TEXT,
  PRIMARY KEY (name)
);
CREATE TABLE Job_Category (
  name VARCHAR(50) NOT NULL,
  PRIMARY KEY(name)
);
CREATE TABLE Payment_Method (
  id INT NOT NULL AUTO_INCREMENT,
  type enum("Credit Card", "Bank Account") NOT NULL,
  PRIMARY KEY (id)
);
CREATE TABLE Wallet (
  id INT NOT NULL AUTO_INCREMENT,
  balance DOUBLE DEFAULT 0,
  automatic BOOLEAN DEFAULT false,
  default_payment_id INT DEFAULT null,
  PRIMARY KEY (id),
  FOREIGN KEY (default_payment_id) REFERENCES Payment_Method(id) ON DELETE
  SET NULL
);
CREATE TABLE Transaction (
  id INT NOT NULL AUTO_INCREMENT,
  amount DOUBLE NOT NULL,
  wallet_id INT NOT NULL,
  payment_method_id INT,
  transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (wallet_id) REFERENCES Wallet(id),
  FOREIGN KEY (payment_method_id) REFERENCES Payment_Method(id) ON DELETE
  SET NULL
);
CREATE TABLE Registered_Payment_Method (
  payment_method_id INT NOT NULL,
  wallet_id INT NOT NULL,
  PRIMARY KEY (payment_method_id),
  FOREIGN KEY (payment_method_id) REFERENCES Payment_Method(id) ON DELETE CASCADE,
  FOREIGN KEY (wallet_id) REFERENCES Wallet(id)
);
CREATE TABLE Credit_Card (
  id INT NOT NULL,
  card_number VARCHAR(16) NOT NULL,
  expiry_date DATE NOT NULL,
  owner_name VARCHAR(100) NOT NULL,
  FOREIGN KEY (id) REFERENCES Payment_Method(id) ON DELETE CASCADE
);
CREATE TABLE Bank_Account (
  id INT NOT NULL,
  account_number VARCHAR(12) NOT NULL,
  owner_name VARCHAR(100) NOT NULL,
  FOREIGN KEY (id) REFERENCES Payment_Method(id) ON DELETE CASCADE
);
CREATE TABLE User_Category (
  name VARCHAR(10) NOT NULL,
  PRIMARY KEY (name)
);
CREATE TABLE Admin (
  id INT NOT NULL AUTO_INCREMENT,
  email VARCHAR(50) NOT NULL,
  category_type VARCHAR(10) NOT NULL DEFAULT 'admin',
  password CHAR(60) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (category_type) REFERENCES User_Category(name),
  CONSTRAINT admin_category_type CHECK (category_type IN ('admin'))
);
CREATE TABLE Seeker (
  id INT NOT NULL AUTO_INCREMENT,
  category_type VARCHAR(10) NOT NULL DEFAULT 'seeker',
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL UNIQUE,
  password CHAR(60) NOT NULL,
  enabled BOOLEAN NOT NULL DEFAULT true,
  plan_name VARCHAR(10) NOT NULL,
  wallet_id INT DEFAULT null,
  date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
  last_connected DATETIME DEFAULT CURRENT_TIMESTAMP,
  security_question_id INT NOT NULL,
  security_answer VARCHAR(255) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (security_question_id) REFERENCES Security_Question(id),
  FOREIGN KEY (plan_name) REFERENCES Seeker_Plan(name) ON UPDATE CASCADE,
  FOREIGN KEY (wallet_id) REFERENCES Wallet(id),
  FOREIGN KEY (category_type) REFERENCES User_Category(name),
  CONSTRAINT seeker_category_type CHECK (category_type IN ('seeker'))
);
CREATE TABLE Employer (
  id INT NOT NULL AUTO_INCREMENT,
  category_type VARCHAR(50) NOT NULL DEFAULT 'employer',
  name VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL UNIQUE,
  password CHAR(60) NOT NULL,
  enabled BOOLEAN NOT NULL DEFAULT true,
  plan_name VARCHAR(10) NOT NULL,
  wallet_id INT NOT NULL,
  date_added DATETIME DEFAULT CURRENT_TIMESTAMP,
  last_connected DATETIME DEFAULT CURRENT_TIMESTAMP,
  security_question_id INT NOT NULL,
  security_answer VARCHAR(255) NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (security_question_id) REFERENCES Security_Question(id),
  FOREIGN KEY (plan_name) REFERENCES Employer_Plan(name) ON UPDATE CASCADE,
  FOREIGN KEY (wallet_id) REFERENCES Wallet(id),
  FOREIGN KEY (category_type) REFERENCES User_Category(name),
  CONSTRAINT employer_category_type CHECK (category_type IN ('employer'))
);
CREATE TABLE Job_Posting (
  id INT NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  max_fill_qty SMALLINT DEFAULT 1,
  fill_qty SMALLINT DEFAULT 0,
  candidate_qty INT DEFAULT 0,
  date_posted DATE NOT NULL,
  category_name VARCHAR(50) NOT NULL,
  employer_id INT NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (category_name) REFERENCES Job_Category(name) ON UPDATE CASCADE,
  FOREIGN KEY (employer_id) REFERENCES Employer(id) ON DELETE CASCADE,
  CONSTRAINT ck_maxqty CHECK (fill_qty <= max_fill_qty)
);
CREATE TABLE Job_Application (
  seeker_id INT NOT NULL,
  job_posting_id INT NOT NULL,
  date_applied DATE NOT NULL,
  status ENUM(
    "applied",
    "offer_pending",
    "employer_denied",
    "seeker_denied",
    "accepted"
  ) DEFAULT "applied",
  PRIMARY KEY (seeker_id, job_posting_id),
  FOREIGN KEY (seeker_id) REFERENCES Seeker(id) ON DELETE CASCADE,
  FOREIGN KEY (job_posting_id) REFERENCES Job_Posting(id) ON DELETE CASCADE
);
drop trigger if exists add_wallet_seeker;
drop trigger if exists add_wallet_employer;
drop trigger if exists add_payment_method_credit_card;
drop trigger if exists add_payment_method_bank_account;
drop trigger if exists delete_wallet_seeker;
drop trigger if exists delete_wallet_employer;
drop trigger if exists default_payment_method_id_insert;
drop trigger if exists default_payment_method_id_update;
drop trigger if exists delete_payment_method;
drop trigger if exists delete_registered_payments;
drop trigger if exists update_application_count_ins;
drop trigger if exists decrease_job_applicants_count;
drop trigger if exists increase_job_applicants_count;
drop trigger if exists update_job_fill_count_on_not_accepted;
drop trigger if exists update_job_fill_count_on_accepted;
drop TRIGGER IF EXISTS check_has_default_for_automatic;
drop trigger if exists remove_applications_on_fill;
drop TRIGGER IF EXISTS payment_method_delete_remove_default;
-- Trigger to concurrently create a wallet entry when creating a seeker
CREATE TRIGGER add_wallet_seeker BEFORE
INSERT ON Seeker FOR EACH ROW BEGIN
INSERT INTO Wallet ()
VALUES ();
SET new.wallet_id = (
    SELECT LAST_INSERT_ID()
  );
-- (SELECT MAX(id) FROM Wallet LIMIT 1);
END;
-- Trigger to concurrently create a wallet entry when creating a employer
CREATE TRIGGER add_wallet_employer BEFORE
INSERT ON Employer FOR EACH ROW BEGIN
INSERT INTO Wallet ()
VALUES ();
SET new.wallet_id = (
    SELECT LAST_INSERT_ID()
  );
-- (SELECT MAX(id) FROM Wallet LIMIT 1);
END;
-- Trigger to concurrently create a payment method entry when adding a credit card
CREATE TRIGGER add_payment_method_credit_card BEFORE
INSERT ON Credit_Card FOR EACH ROW BEGIN
INSERT INTO Payment_Method (type)
VALUES ("Credit Card");
SET new.id = (
    SELECT LAST_INSERT_ID()
  );
-- (SELECT MAX(id) FROM Wallet LIMIT 1);
END;
-- Trigger to concurrently create a payment method entry when adding a bank account
CREATE TRIGGER add_payment_method_bank_account BEFORE
INSERT ON Bank_Account FOR EACH ROW BEGIN
INSERT INTO Payment_Method (type)
VALUES ("Bank Account");
SET new.id = (
    SELECT LAST_INSERT_ID()
  );
-- (SELECT MAX(id) FROM Wallet LIMIT 1);
END;
-- Delete wallet when seeker is deleted
CREATE TRIGGER delete_wallet_seeker
AFTER DELETE ON Seeker FOR EACH ROW BEGIN
DELETE FROM Wallet
WHERE id = old.wallet_id;
END;
-- Delete wallet when employer is deleted
CREATE TRIGGER delete_wallet_employer
AFTER DELETE ON Employer FOR EACH ROW BEGIN
DELETE FROM Wallet
WHERE id = old.wallet_id;
END;
-- Delete registered payment methods when wallet is deleted
CREATE TRIGGER delete_registered_payments BEFORE DELETE ON Wallet FOR EACH ROW BEGIN
DELETE FROM Registered_Payment_Method
WHERE wallet_id = old.id;
END;
-- Delete payment methods when registered remove
CREATE TRIGGER delete_payment_method
AFTER DELETE ON Registered_Payment_Method FOR EACH ROW BEGIN
DELETE FROM Payment_Method
WHERE id = old.payment_method_id;
END;
-- Update job application applicants fill count
CREATE TRIGGER increase_job_applicants_count
AFTER
INSERT ON Job_Application FOR EACH ROW BEGIN
UPDATE Job_Posting
SET candidate_qty = candidate_qty + 1
WHERE id = new.job_posting_id;
END;
CREATE TRIGGER decrease_job_applicants_count
AFTER DELETE ON Job_Application FOR EACH ROW BEGIN
UPDATE Job_Posting
SET candidate_qty = candidate_qty - 1
WHERE id = old.job_posting_id;
END;
-- Update job application fill count
CREATE TRIGGER update_job_fill_count_on_accepted
AFTER
UPDATE ON Job_Application FOR EACH ROW BEGIN
DECLARE newId INT;
DECLARE maxFill INT;
DECLARE currentFill INT;
IF (
  old.status != 'accepted'
  AND new.status = 'accepted'
) THEN
SET newId = NEW.job_posting_id;
SET currentFill = (
    SELECT fill_qty
    FROM Job_Posting
    WHERE id = newId
  );
SET maxFill = (
    SELECT max_fill_qty
    FROM Job_Posting
    WHERE id = newId
  );
SET currentFill = currentFill + 1;
UPDATE Job_Posting
SET fill_qty = currentFill
WHERE id = newId;
END IF;
END;
/* Technically this will never occur in the code */
CREATE TRIGGER update_job_fill_count_on_not_accepted
AFTER
UPDATE ON Job_Application FOR EACH ROW BEGIN
DECLARE newId INT;
DECLARE currentFill INT;
IF (
  old.status = 'accepted'
  AND new.status != 'accepted'
) THEN
SET newId = NEW.job_posting_id;
SET currentFill = (
    SELECT fill_qty
    FROM Job_Posting
    WHERE id = newId
  );
SET currentFill = currentFill - 1;
UPDATE Job_Posting
SET fill_qty = currentFill
WHERE id = newId;
END IF;
END;
/* Sets automatic p */
drop TRIGGER IF EXISTS check_has_default_for_automatic;
CREATE TRIGGER check_has_default_for_automatic BEFORE
UPDATE ON Wallet FOR EACH ROW BEGIN IF (
    NEW.automatic
    AND (NEW.default_payment_id IS NULL)
  ) THEN SIGNAL SQLSTATE '45000';
END IF;
END;
/* Set default payment to null when that payment method is removed */
CREATE TRIGGER payment_method_delete_remove_default BEFORE DELETE ON Payment_Method FOR EACH ROW BEGIN
DECLARE walletId INT;
SET walletId = (
    SELECT id
    FROM Wallet
    WHERE default_payment_id = OLD.id
  );
IF (walletId) THEN
UPDATE Wallet
SET default_payment_id = null
WHERE id = walletId;
END IF;
END;
-- SECURITY QUESTIONS
INSERT INTO Security_Question (id, question)
VALUES (1, "What was your childhood nickname?"),
  (
    2,
    "In what city did you meet your spouse/significant other?"
  ),
  (
    3,
    "What is the name of your favorite childhood friend?"
  ),
  (4, "What is your oldest sibling's middle name?"),
  (5, "What is your oldest cousin's first aame?"),
  (6, "In what city or town did your parents meet?"),
  (
    7,
    "In what city does your nearest sibling live?"
  ),
  (8, "In what city or town was your first job?");
-- SEEKER_PLAN
INSERT INTO Seeker_Plan (
    name,
    monthly_cost,
    application_qty,
    description
  )
VALUES ('basic', 0, 0, 'Viewing Only'),
  ('prime', 10, 5, 'Maximum of 5 applications'),
  (
    'gold',
    20,
    null,
    'Unlimited number of applications'
  );
-- EMPLOYER_PLAN
INSERT INTO Employer_Plan (name, monthly_cost, posting_qty, description)
VALUES ('prime', 50, 5, 'Maximum of 5 job postings'),
  (
    'gold',
    100,
    null,
    'Unlimited number of job postings'
  );
-- USER_CATEGORY
INSERT INTO User_Category (name)
VALUES ('seeker'),
  ('employer'),
  ('admin');