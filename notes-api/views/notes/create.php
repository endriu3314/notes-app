<?php
use NotesApi\Request\Request;

?>

<div class="container">
    <h1 style="text-align: center; margin-bottom: 20px;">Creare notita</h1>
<form action="/app/notes/create" method="post" style="display: flex; flex-direction: column; gap: 10px;">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <input type="text" name="title" placeholder="Titlu" value="<?= Request::getFlash('title', '') ?>">
    <?php if (isset($errors['title'])) { ?>
        <div class="error"><?= $errors['title'] ?></div>
    <?php } ?>

    <textarea name="content" placeholder="Conținut"><?= Request::getFlash('content', '') ?></textarea>
    <?php if (isset($errors['content'])) { ?>
        <div class="error"><?= $errors['content'] ?></div>
    <?php } ?>

    <button type="submit">Salvați</button>
</form>
</div>