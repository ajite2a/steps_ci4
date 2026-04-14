<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? esc($pageTitle) . ' - ' : '' ?>Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <?= $this->renderSection('css') ?>
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .navbar-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .navbar-gradient .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }

        .navbar-gradient .nav-link {
            color: white !important;
            font-weight: 500;
            transition: opacity 0.3s;
        }

        .navbar-gradient .nav-link:hover {
            opacity: 0.8;
        }

        .navbar-gradient .nav-link.active {
            border-bottom: 3px solid white;
            padding-bottom: 0.5rem;
        }

        .btn-logout-custom {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            color: white;
            border-radius: 5px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-logout-custom:hover {
            background: white;
            color: #667eea;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .card-custom {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-gradient sticky-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="<?= route_to('dashboard') ?>">Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= (current_url() == route_to('dashboard')) ? 'active' : '' ?>" href="<?= route_to('dashboard') ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-door me-1" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M8.354 1.146a.5.5 0 0 0-.708 0L1 7.793V14.5A1.5 1.5 0 0 0 2.5 16h3A1.5 1.5 0 0 0 7 14.5V11h2v3.5A1.5 1.5 0 0 0 10.5 16h3a1.5 1.5 0 0 0 1.5-1.5V7.793L8.354 1.146z"/>
                                <path d="M13 2.5V6l.5.5L15 6V2.5a.5.5 0 0 0-.5-.5H13z"/>
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(current_url(), 'suppliers') !== false) ? 'active' : '' ?>" href="<?= route_to('supplier.index') ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people me-1" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M5.5 3a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM11 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/>
                                <path d="M0 13s1-1 4-1 4 1 4 1v1H0v-1zM9 13s1-1 4-1 4 1 4 1v1H9v-1z"/>
                            </svg>
                            Suppliers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(current_url(), 'colors') !== false) ? 'active' : '' ?>" href="<?= route_to('color.index') ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-palette me-1" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M8 0a8 8 0 1 0 7.546 5.032c-.136-.472-.61-.8-1.107-.8-.987 0-1.73.879-1.73 1.962 0 .795-.69 1.438-1.542 1.438C9.4 7.632 8.5 6.84 8.5 5.75 8.5 2.805 6.208 0 3.5 0 2.12 0 1 1.119 1 2.5S2.12 5 3.5 5c.658 0 1.257-.243 1.707-.636A4.992 4.992 0 0 0 8 7a5 5 0 0 0 0-10z"/>
                            </svg>
                            Colors
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(current_url(), 'size-masters') !== false) ? 'active' : '' ?>" href="<?= route_to('sizemaster.index') ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-grid-3x3-gap me-1" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h2A1.5 1.5 0 0 1 6 2.5v2A1.5 1.5 0 0 1 4.5 6h-2A1.5 1.5 0 0 1 1 4.5v-2zM7.5 1A1.5 1.5 0 0 1 9 2.5v2A1.5 1.5 0 0 1 7.5 6h-2A1.5 1.5 0 0 1 4 4.5v-2A1.5 1.5 0 0 1 5.5 1h2zM13.5 1A1.5 1.5 0 0 1 15 2.5v2A1.5 1.5 0 0 1 13.5 6h-2A1.5 1.5 0 0 1 10 4.5v-2A1.5 1.5 0 0 1 11.5 1h2zM1 8.5A1.5 1.5 0 0 1 2.5 7h2A1.5 1.5 0 0 1 6 8.5v2A1.5 1.5 0 0 1 4.5 12h-2A1.5 1.5 0 0 1 1 10.5v-2zM7.5 7A1.5 1.5 0 0 1 9 8.5v2A1.5 1.5 0 0 1 7.5 12h-2A1.5 1.5 0 0 1 4 10.5v-2A1.5 1.5 0 0 1 5.5 7h2zM13.5 7A1.5 1.5 0 0 1 15 8.5v2A1.5 1.5 0 0 1 13.5 12h-2A1.5 1.5 0 0 1 10 10.5v-2A1.5 1.5 0 0 1 11.5 7h2z"/>
                            </svg>
                            Size Masters
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos(current_url(), 'items') !== false) ? 'active' : '' ?>" href="<?= route_to('item.index') ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-seam me-1" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M8.186 1.113a.5.5 0 0 0-.372 0l-7 2A.5.5 0 0 0 .5 3.5v8.662a1 1 0 0 0 .97 1.042c.026 0 .052.01.067.027l5.192 6.856A.5.5 0 0 0 8 20.5a.5.5 0 0 0 .267-.073l5.195-6.856c.015-.018.04-.027.066-.027.55 0 1.002-.523 1.031-1.042V3.5a.5.5 0 0 0-.314-.460l-7-2zM14 4.212V12.5H2V4.212l6-2.001 6 2.001z"/>
                            </svg>
                            Items
                        </a>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white me-3"><?= esc($user_name ?? 'User') ?></span>
                <a href="<?= route_to('logout') ?>" class="btn-logout-custom">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-2 mb-2">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (only loaded when needed) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <?= $this->renderSection('js') ?>
</body>
</html>
