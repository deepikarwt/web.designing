<?php

$conn = new PDO(
    "mysql:host=localhost;dbname=college_db",
    "root",
    ""
);

$sql = "SELECT * FROM students";

$stmt = $conn->query($sql);

header("Content-Type: text/csv");

header(
    "Content-Disposition: attachment; filename=students.csv"
);

$output = fopen("php://output", "w");

fputcsv(
    $output,
    ['ID', 'Name', 'Email', 'Enrollment Date']
);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    fputcsv($output, $row);

}

fclose($output);

exit();

?>