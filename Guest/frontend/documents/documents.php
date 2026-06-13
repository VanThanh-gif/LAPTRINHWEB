<?php

$documents = [
    [
        "id" => 1,
        "title" => "Lập trình PHP cơ bản",
        "author" => "Nguyễn Văn A",
        "date" => "01/06/2026",
        "category" => "Lập trình"
    ],
    [
        "id" => 2,
        "title" => "Lập trình Java nâng cao",
        "author" => "Trần Văn B",
        "date" => "03/06/2026",
        "category" => "Lập trình"
    ],
    [
        "id" => 4,
        "title" => "Cơ sở dữ liệu",
        "author" => "Phạm Thị D",
        "date" => "07/06/2026",
        "category" => "Database"
    ]
];

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách tài liệu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f5f7fb;
        }

        .hero{
            background:linear-gradient(135deg,#0d6efd,#4f46e5);
            color:white;
            padding:60px 0;
        }

        .document-card{
            transition:0.3s;
            border:none;
            border-radius:15px;
        }

        .document-card:hover{
            transform:translateY(-5px);
            box-shadow:0 10px 25px rgba(0,0,0,0.15);
        }

        footer{
            margin-top:60px;
            background:#212529;
            color:white;
            padding:20px;
            text-align:center;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <a class="navbar-brand" href="#">
            📚 Document Sharing
        </a>

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menu">

            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#">
                        Trang chủ
                    </a>
                </li>

               <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle"
       href="#"
       id="documentsDropdown"
       role="button"
       data-bs-toggle="dropdown"
       aria-expanded="false">
        Tài liệu
    </a>

    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a class="dropdown-item" href="#">
                📚 Danh sách tài liệu
            </a>
        </li>

        <li>
            <a class="dropdown-item" href="#">
                ⬆️ Upload tài liệu
            </a>
        </li>

        <li>
            <a class="dropdown-item" href="#">
                🔍 Tìm kiếm tài liệu
            </a>
        </li>

        <li>
            <a class="dropdown-item" href="#">
                📂 Lọc theo môn học
            </a>
        </li>

        <li>
            <a class="dropdown-item" href="#">
                📥 Tải xuống tài liệu
            </a>
        </li>

        <li>
            <a class="dropdown-item" href="#">
                📁 Tài liệu của tôi
            </a>
        </li>
    </ul>
</li>
<li class="nav-item">
    <a class="nav-link" href="ai_assistant.php">
        AI Chatbot
    </a>
</li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        Đăng nhập
                    </a>
                </li>
            </ul>
        </div>

    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <div class="container text-center">

        <h1>Kho Tài Liệu Học Tập</h1>

        <p class="mt-3">
            Chia sẻ và tìm kiếm tài liệu học tập dễ dàng
        </p>

        <div class="row justify-content-center mt-4">
            <div class="col-md-6">

                <input
                    type="text"
                    class="form-control form-control-lg"
                    placeholder="Tìm kiếm tài liệu..."
                >

            </div>
        </div>

    </div>
</section>

<!-- Danh sách tài liệu -->
<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>Danh sách tài liệu</h2>

        <span class="badge bg-primary">
            <?= count($documents) ?> tài liệu
        </span>

    </div>

    <div class="row">

        <?php foreach($documents as $doc): ?>

            <div class="col-md-6 col-lg-4 mb-4">

                <div class="card document-card h-100">

                    <div class="card-body">

                        <h5 class="card-title">
                            📄 <?= $doc['title'] ?>
                        </h5>

                        <p class="text-muted mb-2">
                            Tác giả:
                            <?= $doc['author'] ?>
                        </p>

                        <p>
                            <span class="badge bg-secondary">
                                <?= $doc['category'] ?>
                            </span>
                        </p>

                        <p class="small text-muted">
                            Ngày đăng:
                            <?= $doc['date'] ?>
                        </p>

                    </div>

                    <div class="card-footer bg-white border-0">

                        <a href="document_detail.php?id=<?= $doc['id'] ?>"
                           class="btn btn-primary w-100">

                            Xem chi tiết

                        </a>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

<!-- Footer -->
<footer>
    <p>
        © 2026 Document Sharing System
    </p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
