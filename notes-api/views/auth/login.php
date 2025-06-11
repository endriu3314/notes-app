<?php

use NotesApi\Request\Request;

?>


<div class="container">
    <h1 style="text-align: center; margin-bottom: 20px;">Autentificare - Aplicatie de notite</h1>
    <form action="/auth/login" method="post" style="display: flex; flex-direction: column; gap: 10px;">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <input type="text" name="email" placeholder="Email" value="<?php echo Request::getFlash('email', ''); ?>">
        <?php if (isset($errors['email'])): ?>
            <div class="error"><?php echo $errors['email']; ?></div>
        <?php endif; ?>

        <input type="password" name="password" placeholder="Password"
            value="<?php echo Request::getFlash('password', ''); ?>">
        <?php if (isset($errors['password'])): ?>
            <div class="error"><?php echo $errors['password']; ?></div>
        <?php endif; ?>

        <button type="submit">Login</button>
    </form>

    <div style="margin-top: 10px;">
        <a href="/auth/register">Register</a>
    </div>
</div>