<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$title ?? 'Event Management System'?></title>

    <!-- Bootstrap CSS with local fallback -->
    <link href="<?= BASE_PATH ?>/assets/css/bootstrap.min.css" rel="stylesheet"
        onerror="this.onerror=null;this.href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css'">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .navbar-brand {
        font-weight: 600;
        font-size: 1.5rem;
    }

    .nav-link {
        font-weight: 500;
        padding: 0.5rem 1rem !important;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }

    .required::after {
        content: " *";
        color: #dc3545;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }

    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    </style>
</head>

<body class="d-flex flex-column h-100">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= BASE_PATH ?>/">
                <i class="bi bi-calendar-event me-2"></i>
                Event Management
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($user)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_PATH ?>/events">
                            <i class="bi bi-calendar2-week me-1"></i> Events
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_PATH ?>/logout">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_PATH ?>/login">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_PATH ?>/register">
                            <i class="bi bi-person-plus me-1"></i> Register
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main class="flex-shrink-0">
        <div class="container">