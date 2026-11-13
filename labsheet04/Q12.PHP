<?php

$conn = new PDO(
    "mysql:host=localhost;dbname=college_db",
    "root",
    ""
);

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {

        $message = "Login Successful!";

    } else {

        $message = "Invalid Username or Password!";

    }

}

?>

<form method="POST">

    Username:
    <input type="text" name="username" required>
    <br><br>

    Password:
    <input type="password" name="password" required>
    <br><br>

    <button type="submit">Login</button>

</form>

<h3><?php echo htmlspecialchars($message); ?></h3>