CREATE USER 'college_user'@'localhost'
IDENTIFIED BY 'college123';

GRANT SELECT, INSERT, UPDATE
ON college_db.*
TO 'college_user'@'localhost';
