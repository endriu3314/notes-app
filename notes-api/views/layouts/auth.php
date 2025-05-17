<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Notes App' ?></title>
    <link rel="stylesheet" href="/assets/reset.css">
    <link rel="stylesheet" href="/assets/auth.css">
</head>
<body>
    <main>
        <div class="page">
            <?php include $content; ?>
        </div>
    </main>
</body>
</html>