<?php

session_start();

$_SESSION['username'] = "Rahul";

echo "Session Created Successfully!";

echo "<br>";

echo "Welcome " . $_SESSION['username'];

echo "<br><br>";

echo "<a href='Q15_second.php'>Go to Second Page</a>";

?>