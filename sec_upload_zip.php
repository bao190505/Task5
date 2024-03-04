<?php
session_start();

$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) { // kiểm tra có thư mục chưa nếu không tạo thư mục.
    mkdir($uploadDir);
}
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $uploadPath = $uploadDir . $file['name'];

    if (pathinfo($uploadPath, PATHINFO_EXTENSION) === 'zip') { // kiểm tra file có phải file zip không.
        $zip = new ZipArchive();
        if ($zip->open($uploadPath) === TRUE) {
            for ($i = 0; $i < $zip->numFiles; $i++){ // lặp qua từng file có trong file zip
                $fileName = $zip->getNameIndex($i);
                $fileContents = $zip->getFromIndex($i);
                // Kiểm tra xem tệp có phải là symbolic link không
                if (!is_dir($fileName) && is_link($extracDir)) { // nếu là symlink cho dừng xuất ra thông báo.
                    exit( "Tệp $fileName trong ZIP là symbolic link");
                    }
            }
            $characterAllow = "/^[a-zA-Z1-9.]+$/"; // các kí tự được cho phép
            if (!preg_match($characterAllow,$fileName)){ //kiểm tra có kí tự nào khác không 
                exit("file không hợp lệ");
            }
            $fileName = realpath($fileName);
            $extractedPath = $uploadDir.$fileName;
            file_put_contents($extractedPath, $fileContents);
            $resultMessage = "<p>File unzipped <a href='".$uploadDir."'>here</a>.</p>";
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
    <form action="sec.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="file" id="file">
        <input type="submit" name="upload" value="Upload">
    </form>
</body>
</html>
