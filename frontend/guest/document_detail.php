<?php
require_once '../../../Admin/backend/config/connectdb.php';

if (!isset($_GET['id'])) {
    die("Không tìm thấy tài liệu");
}

$document_id = (int)$_GET['id'];

$sql = "SELECT * FROM documents WHERE document_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $document_id);
$stmt->execute();

$result = $stmt->get_result();
$document = $result->fetch_assoc();

if (!$document) {
    die("Tài liệu không tồn tại");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($document['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <h2><?= htmlspecialchars($document['title']) ?></h2>

    <hr>

    <p>
        <strong>Mô tả:</strong><br>
        <?= nl2br(htmlspecialchars($document['description'])) ?>
    </p>

    <p>
        <strong>Tên file:</strong>
        <?= htmlspecialchars($document['file_name']) ?>
    </p>

    <p>
        <strong>Loại file:</strong>
        <?= htmlspecialchars($document['file_type']) ?>
    </p>

    <p>
        <strong>Kích thước:</strong>
        <?= number_format($document['file_size']) ?> bytes
    </p>

    <p>
        <strong>Ngày tải lên:</strong>
        <?= $document['upload_date'] ?>
    </p>

    <a href="../../../User/backend/documents/download_document.php?id=<?= $document['document_id'] ?>"
       class="btn btn-success">
        Tải xuống
    </a>

    <a href="documents.php" class="btn btn-secondary">
        Quay lại
    </a>

</div>

</body>
</html>