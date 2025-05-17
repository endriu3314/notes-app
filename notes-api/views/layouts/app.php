<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Notes App' ?></title>
    <link rel="stylesheet" href="/assets/reset.css">
    <link rel="stylesheet" href="/assets/app.css">
</head>

<body>
    <nav style="display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background-color: #f0f0f0;
    border-bottom: 1px solid #e0e0e0;
    ">
        <a href="/app/dashboard">
            <h3>Dashboard</h3>
        </a>
        <div style="display: flex; gap: 1rem;">
            <a class="nav-link">Sesiuni</a>
            <form action="/auth/logout" method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <button class="nav-link" type="submit">Logout</button>
            </form>
        </div>
    </nav>

    <main>
        <?php include $content; ?>
    </main>
</body>

</html>