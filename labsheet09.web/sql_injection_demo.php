<?php
// SECURITY DEMONSTRATION ONLY.
// Do not execute the vulnerable pattern with real user input.

// Vulnerable pattern:
// $sql = "SELECT * FROM users WHERE email='" . $_GET['email'] . "'";

// Prepared-statement version:
require_once 'common.php';

$email = $_GET['email'] ?? '';

$stmt = $db->prepare(
    'SELECT id, email FROM users WHERE email = ?'
);
$stmt->bind_param('s', $email);
$stmt->execute();

$result = $stmt->get_result();

echo 'Prepared query returned ' . $result->num_rows . ' record(s).';