<?php
use NotesApi\Request\Request;

?>



<div class="container">
<dialog id="authorize-user-dialog" style="margin: auto; padding: 1rem; min-width: 600px;">
    <div style="display: flex; justify-content: space-between;">
    <h1>Adaugare utilizator</h1>
    <button type="button" class="btn" onclick="document.getElementById('authorize-user-dialog').close()">x</button>
    </div>
    
    <form action="/app/notes/<?= $note->id ?>/authorize" method="post" style="display: flex; flex-direction: column; gap: 10px; margin-top: 1rem;">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="email" name="email" placeholder="Email">
        <button class="btn" type="submit">Adauga</button>
    </form>
</dialog>

    <form id="note-form" action="/app/notes/<?= $note->id ?>" method="post"
        style="display: flex; flex-direction: column; gap: 20px;">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input id="method-input" type="hidden" name="_method" value="OPTIONS">

        <div style="display: flex; justify-content: space-between;">
            <div>
                <h1><input type="text" name="title" value="<?= $note->title ?>" style="width: 100%;"></h1>
                <?php if (isset($errors['title'])) { ?>
                    <div class="error"><?= $errors['title'] ?></div>
                <?php } ?>
                <h4>Autor: <?= $note->user->name ?></h4>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px; align-items: flex-end;">
                <button 
                    class="btn btn-danger" 
                    type="submit" 
                    <?= Request::getInstance()->user()->id === $note->user->id ? '' : 'disabled' ?>
                    data-method="DELETE"
                >
                    Delete
                </button>
                <?php if (isset($errors['delete'])) { ?>
                    <div class="error"><?= $errors['delete'] ?></div>
                <?php } ?>
                <h6>Data ultimei modificari: <?= $note->updatedAt ?></h6>
                <h6>Data crearii: <?= $note->createdAt ?></h6>
            </div>
        </div>

        <div style="margin-top: 20px;">
        <h3>Utilizatori autorizati</h3>
        <?php if (isset($errors['unauthorize'])) { ?>
            <div class="error"><?= $errors['unauthorize'] ?></div>
        <?php } ?>
        <div style="display: flex; overflow-x: auto; gap: 10px;">
            <?php foreach ($note->authorizedUsers as $user) { ?>
                <div
                    style="display: flex; flex-direction: row; gap: 10px; border: 1px solid #000; align-items: center; padding: 10px; border-radius: 5px;">
                    <div><?= $user->name ?></div>
                    <?php if (Request::getInstance()->user()->id === $note->user->id) { ?>
                        <button class="btn btn-danger" type="button" onclick="unauthorizeUser(<?= $user->id ?>)">x</button>
                    <?php } ?>
                </div>
            <?php } ?>
            <button class="btn" type="button" onclick="document.getElementById('authorize-user-dialog').showModal()">+</button>
        </div>
    </div>

        <textarea name="content" placeholder="Content" rows="10"
            value="<?= $note->content ?>"><?= $note->content ?></textarea>
        <?php if (isset($errors['content'])) { ?>
            <div class="error"><?= $errors['content'] ?></div>
        <?php } ?>
        <button class="btn" type="submit" data-method="PUT">Save</button>
    </form>

    <script>
        const methodInput = document.getElementById("method-input");

        const submitButtons = document.querySelectorAll('button[type="submit"][data-method]');
        submitButtons.forEach(button => {
            button.addEventListener('click', function () {
                const intendedMethod = this.dataset.method;
                if (intendedMethod) {
                    methodInput.value = intendedMethod.toUpperCase();
                    console.log(`_method set to: ${methodInput.value} for ${this.id}`);
                }
            });
        });

        function unauthorizeUser(userId) {
            if (! confirm(`Esti sigur ca vrei sa dezautorizezi utilizatorul?`)) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/app/notes/<?= $note->id ?>/unauthorize/${userId}`;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = '<?= $_SESSION['csrf_token'] ?>';
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);

            form.submit();
        }
    </script>

    <a href="/app/notes">Back to notes</a>
</div>