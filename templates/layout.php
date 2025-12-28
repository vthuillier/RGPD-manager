<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'RGPD Manager' ?></title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --error: #ef4444;
            --success: #22c55e;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .nav-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .navbar a {
            text-decoration: none;
            color: var(--text);
            font-weight: 500;
        }

        .navbar a.brand {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.25rem;
        }

        .container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            background: var(--card-bg);
            border-radius: 0.5rem;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        h1 {
            margin-top: 0;
        }

        .btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
            transition: background 0.2s;
        }

        .btn:hover {
            background: var(--primary-hover);
        }

        .btn-danger {
            background: var(--error);
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .btn-outline:hover {
            background: #f1f5f9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th,
        td {
            text-align: left;
            padding: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        th {
            background: #f1f5f9;
            font-weight: 600;
        }

        form div {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--border);
            border-radius: 0.375rem;
            box-sizing: border-box;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .user-nav {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.875rem;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <a href="index.php" class="brand">RGPD Manager</a>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?page=treatment&action=dashboard">Tableau de bord</a>
                <a href="index.php?page=treatment&action=list">Registre</a>
                <div class="user-nav">
                    <span>Bonjour, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong></span>
                    <a href="index.php?page=auth&action=logout" class="btn btn-outline">Déconnecter</a>
                </div>
            <?php else: ?>
                <a href="index.php?page=auth&action=login">Connexion</a>
                <a href="index.php?page=auth&action=register" class="btn">S'inscrire</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <?= $content ?>
    </div>
</body>

</html>