<?php

require_once '../../../Admin/backend/config/connectdb.php';

$id = $_GET['id'];

$sql =
"SELECT * FROM documents
 WHERE document_id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i",$id);

$stmt->execute();

$result = $stmt->get_result();

$doc = $result->fetch_assoc();
?>

<h2>Chi tiết tài liệu</h2>

<p>
<b>Tiêu đề:</b>
<?= $doc['title'] ?>
</p>

<p>
<b>Mô tả:</b>
<?= $doc['description'] ?>
</p>

<p>
<b>Tên file:</b>
<?= $doc['file_name'] ?>
</p>

<p>
<b>Loại:</b>
<?= $doc['file_type'] ?>
</p>

<p>
<b>Kích thước:</b>
<?= $doc['file_size'] ?>
 bytes
</p>
