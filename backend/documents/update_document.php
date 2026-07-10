<?php

require_once '../../../Admin/backend/config/connectdb.php';

$id = $_POST['document_id'];

$title = $_POST['title'];

$description = $_POST['description'];

$sql =
"UPDATE documents
SET
title=?,
description=?
WHERE document_id=?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
"ssi",
$title,
$description,
$id
);

$stmt->execute();

header(
"Location: ../../frontend/documents/my_documents.php"
);
