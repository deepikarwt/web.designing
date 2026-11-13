<?php

$message = "";

if (isset($_POST['upload'])) {

    $folder = "uploads/";

    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $file = $_FILES['image'];

    $allowed = ['jpg', 'jpeg', 'png'];

    $extension = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );

    if ($file['error'] !== UPLOAD_ERR_OK) {

        $message = "File upload failed!";

    } elseif ($file['size'] > 2 * 1024 * 1024) {

        $message = "File size must be below 2 MB!";

    } elseif (!in_array($extension, $allowed)) {

        $message = "Only JPG, JPEG and PNG files allowed!";

    } else {

        $imageInfo = getimagesize($file['tmp_name']);

        if ($imageInfo === false) {

            $message = "Invalid image file!";

        } else {

            $filename = uniqid("image_", true) . "." . $extension;

            if (move_uploaded_file(
                $file['tmp_name'],
                $folder . $filename
            )) {

                $message = "Image Uploaded Successfully!";

            } else {

                $message = "Unable to upload image!";

            }

        }

    }

}

?>

<!DOCTYPE html>
<html>
<body>

<h2>Upload Image</h2>

<form method="POST" enctype="multipart/form-data">

    Select Image:

    <input type="file" name="image" required>

    <br><br>

    <button type="submit" name="upload">
        Upload
    </button>

</form>

<h3><?php echo htmlspecialchars($message); ?></h3>

</body>
</html>