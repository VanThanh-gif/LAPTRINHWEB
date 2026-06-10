<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tải tài liệu</title>
</head>
<body>

<h2>Tải tài liệu lên</h2>

<form action="../../backend/documents/upload_document.php"
      method="POST"
      enctype="multipart/form-data">

    <label>Tiêu đề</label><br>
    <input type="text" name="title" required><br><br>

    <label>Mô tả</label><br>
    <textarea name="description"></textarea><br><br>

    <label>Chọn file</label><br>
    <input type="file" name="document" required><br><br>

    <button type="submit">
        Upload
    </button>

</form>

</body>
</html>
