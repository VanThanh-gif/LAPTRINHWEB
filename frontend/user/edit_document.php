<?php

require_once '../../../Admin/backend/config/connectdb.php';
$id = $_GET['id'];

$stmt =
$conn->prepare(
"SELECT * FROM documents
 WHERE document_id=?"
);

$stmt->bind_param("i",$id);

$stmt->execute();

$result=$stmt->get_result();

$doc=$result->fetch_assoc();
?>

<form
action="../../backend/documents/update_document.php"
method="POST">

<input
type="hidden"
name="document_id"
value="<?= $doc['document_id'] ?>">

Tiêu đề

<input
type="text"
name="title"
value="<?= $doc['title'] ?>">

<br><br>

Mô tả

<textarea
name="description"><?= $doc['description'] ?></textarea>

<br><br>

<button type="submit">
Cập nhật
</button>

</form>
