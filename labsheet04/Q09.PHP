<?php

$conn = new PDO(
    "mysql:host=localhost;dbname=college_db",
    "root",
    ""
);

if (isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    $sql = "DELETE FROM students WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo "Student Deleted Successfully!";
    } else {
        echo "Student Record Not Found!";
    }

} else {

    echo "Please provide student ID.";

}

?>