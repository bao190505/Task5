<?php
session_start();

// Kiểm tra và tạo thư mục tạm thời
$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir);
}

// Xử lý upload file
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];

    // Xác định đường dẫn lưu trữ
    $uploadPath = $uploadDir . $file['name'];

    // Kiểm tra và giải nén file zip
    if (pathinfo($uploadPath, PATHINFO_EXTENSION) === 'zip') {
        $zip = new ZipArchive();
        if ($zip->open($uploadPath) === TRUE) {

            // Tạo thư mục để lưu trữ nội dung giải nén với tên ngẫu nhiên
            $extractDir = $uploadDir . 'extracted/';
            if (!file_exists($extractDir)) {
                mkdir($extractDir);
            }

            // Lặp qua từng file trong zip và lưu trữ nội dung
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $fileContents = $zip->getFromIndex($i);
                $extractedPath = $extractDir . $filename;

                file_put_contents($extractedPath, $fileContents);
            }

            $resultMessage = 'File đã được upload và giải nén thành công.';

            // Đóng file zip
            $zip->close();
        } else {
            $resultMessage = 'Có lỗi khi giải nén file.';
        }
    } else {
        $resultMessage = 'File không phải là file zip.';
    }

    // Di chuyển file tải lên vào thư mục lưu trữ
    move_uploaded_file($file['tmp_name'], $uploadPath);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload và Giải Nén File Zip</title>
</head>
<body>
    <h2>Upload và Giải Nén File Zip</h2>
    <?php echo $resultMessage; ?>
    <form action="index.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="file" id="file">
        <input type="submit" name="upload" value="Upload">
    </form>
</body>
</html>