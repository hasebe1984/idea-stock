DROP DATABASE IF EXISTS ideastock;
CREATE DATABASE IF NOT EXISTS ideastock DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE ideastock;

DROP TABLE IF EXISTS user;

CREATE TABLE user (
  id INT auto_increment,
  loginId varchar(10) NOT NULL,
  check (char_length(loginId) >= 8),
  password varchar(255) NOT NULL, 
  check (char_length(password) >= 6),
  name VARCHAR(10) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE (loginId)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS question;

CREATE TABLE question (
  id INT auto_increment,
  userId INT NOT NULL,
  question varchar(256) NOT NULL,
  date DATETIME NOT NULL,
  deleteFlg TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  FOREIGN KEY (userId) REFERENCES user(id)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



DROP TABLE IF EXISTS answer;

CREATE TABLE answer (
  id BigINT auto_increment,
  questionId INT NOT NULL,
  userId INT NOT NULL,
  answer VARCHAR (256) NOT NULL,
  date DATETIME NOT NULL,
  deleteFlg TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  FOREIGN KEY (questionId) REFERENCES question(id),
  FOREIGN KEY (userId) REFERENCES user(id)
) DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;