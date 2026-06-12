<?php

session_start();

require_once '../../../Admin/backend/config/connectdb.php';

$user_id = $_SESSION['user_id'];

$id = $_GET['id'];

$stmt =
$conn->prepare(
"SELECT *
FROM documents
WHERE document_id=?
AND user_id=?"
);

$stmt->bind_param(
"ii",
$id,
$user_id
);

$stmt->execute();

$result = $stmt->get_result();

$doc = $result->fetch_assoc();

if($doc)
{
    if(file_exists($doc['file_path']))
    {
        unlink($doc['file_path']);
    }

    $delete =
    $conn->prepare(
    "DELETE FROM documents
     WHERE document_id=?"
    );

    $delete->bind_param("i",$id);

    $delete->execute();
}

header(
"Location: ../../frontend/documents/my_documents.php"
);
