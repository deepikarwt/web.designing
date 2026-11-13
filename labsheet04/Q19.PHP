<?php

$conn = new PDO(
    "mysql:host=localhost;dbname=college_db",
    "root",
    ""
);

try {

    $conn->beginTransaction();

    $stmt = $conn->prepare(
        "UPDATE accounts
         SET balance = balance - ?
         WHERE id = ?"
    );

    $stmt->execute([1000, 1]);

    $stmt = $conn->prepare(
        "INSERT INTO transactions
         (account_id, amount)
         VALUES (?, ?)"
    );

    $stmt->execute([1, 1000]);

    $conn->commit();

    echo "Transaction Completed Successfully!";

} catch (Exception $e) {

    $conn->rollBack();

    echo "Transaction Failed!";

}

?>