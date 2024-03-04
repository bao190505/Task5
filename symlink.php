<?php
session_start();

$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir);
}
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $uploadPath = $uploadDir . $file['name'];

    if (pathinfo($uploadPath, PATHINFO_EXTENSION) === 'zip') {
        $zip = new ZipArchive();
        if ($zip->open($uploadPath) === TRUE) {
            $cmd = "unzip $uploadPath -d $uploadDir";

            $result = shell_exec($cmd);
            $resultMessage = "<p>File unzipped <a href='".$uploadDir."'>here</a>.</p>";
            $zip->close();
        } else {
            $resultMessage = 'Có lỗi khi giải nén file.';
        }
    } else {
        $resultMessage = 'File không phải là file zip.';
    }

    move_uploaded_file($file['tmp_name'], $uploadPath);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File Zip</title>
</head>
<body>
    <h2>Upload File Zip</h2>
    <?php echo $resultMessage; ?>
    <?php echo $result; ?>
    <form action="symlink.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="file" id="file">
        <input type="submit" name="upload" value="Upload">
    </form>
</body>
</html>
