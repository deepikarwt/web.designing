<?php

$conn = new PDO(
    "mysql:host=localhost;dbname=college_db",
    "root",
    ""
);

$sql = "CREATE TABLE IF NOT EXISTS students (

    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    enrollment_date DATE

)";

$conn->exec($sql);

echo "Students Table Created Successfully!";

?>