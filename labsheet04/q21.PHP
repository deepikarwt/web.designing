*/

$password = "12345";

$hash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

echo "<br>Hashed Password: " . $hash;

if (password_verify("12345", $hash)) {

    echo "<br>Password Verified Successfully!";

} else {

    echo "<br>Invalid Password!";

}

?>