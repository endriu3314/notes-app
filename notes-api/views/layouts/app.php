<?php
use NotesApi\Request\Request;

?>

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
    margin-bottom: 20px;
    ">
        <a href="/app/dashboard">
            <h3>Dashboard</h3>
        </a>
        <div style="display: flex; gap: 1rem;">
            <a class="nav-link" href="/app/notes">Notite</a>
            <a class="nav-link" href="/app/sessions">Sesiuni</a>
            <form action="/auth/logout" method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <button class="nav-link" type="submit">Logout</button>
            </form>
        </div>
    </nav>

    <main>
        <?php if ($success = Request::getFlash('success')) { ?>
            <div style="background: rgba(205, 254, 194, 0.4); margin: 20px 40px; padding: 8px; border-radius: 5px; border: 1px solid rgba(205, 254, 194, 1);">
                <div class="success"><?= $success ?></div>
            </div>
        <?php } ?>

        <?php if ($error = Request::getFlash('error')) { ?>
            <div style="background: rgba(254, 194, 194, 0.4); margin: 20px 40px; padding: 8px; border-radius: 5px; border: 1px solid rgba(254, 194, 194, 1);">
                <div class="error"><?= $error ?></div>
            </div>
        <?php } ?>

        <?php include $content; ?>
    </main>
</body>

</html>