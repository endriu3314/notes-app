<?php

use NotesApi\Request\Request;

?>


<div class="container">
    <h1 style="text-align: center; margin-bottom: 20px;">Inregistrare - Aplicatie de notite</h1>
    <form action="/auth/register" method="post" style="display: flex; flex-direction: column; gap: 10px;">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <input type="text" name="name" placeholder="Name" value="<?php echo Request::getFlash('name', ''); ?>">
        <?php if (isset($errors['name'])) { ?>
            <div class="error"><?php echo $errors['name']; ?></div>
        <?php } ?>

        <input type="text" name="email" placeholder="Email" value="<?php echo Request::getFlash('email', ''); ?>">
        <?php if (isset($errors['email'])) { ?>
            <div class="error"><?php echo $errors['email']; ?></div>
        <?php } ?>

        <input type="password" name="password" placeholder="Password"
            value="<?php echo Request::getFlash('password', ''); ?>">
        <?php if (isset($errors['password'])) { ?>
            <div class="error"><?php echo $errors['password']; ?></div>
        <?php } ?>

        <button type="submit">Register</button>
    </form>

    <div style="margin-top: 10px;">
        <a href="/auth/login">Login</a>
    </div>
</div>