<?php
session_start();

require_once '../../../Admin/backend/config/connectdb.php';

$user_id = $_SESSION['user_id'];

$sql = "
SELECT *
FROM documents
WHERE user_id = ?
ORDER BY upload_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$user_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<h2>Tài liệu của tôi</h2>

<a href="upload.php">
Tải tài liệu mới
</a>

<hr>

<table border="1">

<tr>
<th>ID</th>
<th>Tiêu đề</th>
<th>Ngày tải</th>
<th>Thao tác</th>
</tr>

<?php while($row=$result->fetch_assoc()) { ?>

<tr>

<td>
<?= $row['document_id'] ?>
</td>

<td>
<?= $row['title'] ?>
</td>

<td>
<?= $row['upload_date'] ?>
</td>

<td>

<a href="document_detail.php?id=<?= $row['document_id'] ?>">
Xem
</a>

<a href="edit_document.php?id=<?= $row['document_id'] ?>">
Sửa
</a>

<a href="../../backend/documents/delete_document.php?id=<?= $row['document_id'] ?>">
Xóa
</a>

<a href="../../backend/documents/download_document.php?id=<?= $row['document_id'] ?>">
Tải
</a>

</td>

</tr>

<?php } ?>

</table>
