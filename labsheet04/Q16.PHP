<?php

if (isset($_POST['theme'])) {

    $theme = $_POST['theme'];

    setcookie(
        "user_theme",
        $theme,
        time() + 86400,
        "/"
    );

    header("Location: Q16.php");
    exit();

}

$theme = $_COOKIE['user_theme'] ?? "white";

?>

<!DOCTYPE html>
<html>
<head>

<title>Cookie Example</title>

</head>

<body style="background-color: <?php echo htmlspecialchars($theme); ?>;">

<h2>Cookie Management</h2>

<form method="POST">

    Select Theme:

    <select name="theme">

        <option value="white">White</option>

        <option value="lightblue">Blue</option>

        <option value="lightgreen">Green</option>

    </select>

    <button type="submit">Save Theme</button>

</form>

<p>Selected Theme: <?php echo htmlspecialchars($theme); ?></p>

</body>
</html>