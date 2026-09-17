<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - E-commerce Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="login-page">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card login-card shadow-lg" style="max-width: 420px; width: 100%;">
            <div class="card-header bg-primary text-white text-center py-4">
                <i class="bi bi-shop fs-2"></i>
                <h2 class="mb-0 mt-1">E-commerce Portal</h2>
                <p class="mb-0 small">Product &amp; Inventory Management</p>
            </div>
            <div class="card-body p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
