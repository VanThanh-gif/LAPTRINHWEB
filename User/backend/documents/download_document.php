<?php

require_once '../../../config/database.php';

$id = $_GET['id'];

$stmt =
$conn->prepare(
"SELECT *
FROM documents
WHERE document_id=?"
);

$stmt->bind_param("i",$id);

$stmt->execute();

$result = $stmt->get_result();

$doc = $result->fetch_assoc();

if($doc)
{
    header(
    'Content-Type: application/octet-stream'
    );

    header(
    'Content-Disposition: attachment; filename="'.$doc['file_name'].'"'
    );

    readfile($doc['file_path']);
}
