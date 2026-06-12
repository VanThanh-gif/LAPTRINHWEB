<?php
session_start();

require_once '../../../Admin/backend/config/connectdb.php';

$user_id = $_SESSION['user_id'];

$title = $_POST['title'];
$description = $_POST['description'];

$uploadDir = "../../../uploads/";

if(!file_exists($uploadDir))
{
    mkdir($uploadDir,0777,true);
}

$fileName = time().'_'.$_FILES['document']['name'];

$filePath = $uploadDir.$fileName;

move_uploaded_file(
    $_FILES['document']['tmp_name'],
    $filePath
);

$sql = "INSERT INTO documents
(
user_id,
title,
description,
file_name,
file_path,
file_type,
file_size
)
VALUES
(?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
"issssis",
$user_id,
$title,
$description,
$fileName,
$filePath,
$_FILES['document']['type'],
$_FILES['document']['size']
);

$stmt->execute();

header(
"Location: ../../frontend/documents/my_documents.php"
);
