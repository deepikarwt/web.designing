<?php

$conn = new PDO(
    "mysql:host=localhost;dbname=college_db",
    "root",
    ""
);

$sql = "SELECT * FROM students";

$stmt = $conn->query($sql);

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
<title>Student Records</title>

<style>

table {
    border-collapse: collapse;
    width: 70%;
}

th, td {
    border: 1px solid black;
    padding: 10px;
    text-align: center;
}

th {
    background-color: lightblue;
}

</style>
</head>

<body>

<h2>Student Records</h2>

<table>

<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Enrollment Date</th>
</tr>

<?php foreach ($students as $row) { ?>

<tr>
    <td><?= htmlspecialchars($row['id']) ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= htmlspecialchars($row['enrollment_date']) ?></td>
</tr>

<?php } ?>

</table>

</body>
</html>