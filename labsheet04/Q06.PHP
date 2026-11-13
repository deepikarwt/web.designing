<?php

$conn = new PDO(
    "mysql:host=localhost;dbname=college_db",
    "root",
    ""
);

$sql = "INSERT INTO students
        (name, email, enrollment_date)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->execute([
    "Rahul",
    "rahul@gmail.com",
    date("Y-m-d")
]);

echo "Student Record Inserted Successfully!";

?>