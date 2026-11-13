<?php

$conn = new PDO(
    "mysql:host=localhost;dbname=college_db",
    "root",
    ""
);

$id = 1;
$new_email = "rahul123@gmail.com";

$sql = "UPDATE students
        SET email = ?
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->execute([$new_email, $id]);

if ($stmt->rowCount() > 0) {
    echo "Student Email Updated Successfully!";
} else {
    echo "No record updated. Check the student ID.";
}

?>