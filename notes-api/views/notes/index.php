<?php
use NotesApi\Request\Request;

?>

<div class="container">
<div style="display: flex; flex-direction: column; gap: 1rem; padding: 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1>Notite</h1>

        <div style="display: flex; gap: 1rem; align-items: center;">
            <input type="checkbox" id="onlyPersonalFilter" <?= filter_var(Request::getInstance()->get('onlyPersonal'), FILTER_VALIDATE_BOOL) ? 'checked' : '' ?> onchange="onlyPersonalFilterChanged()" />
            <label for="onlyPersonal">Doar notitele mele</label>

            <script>
                function onlyPersonalFilterChanged() {
                    const checkbox = document.getElementById('onlyPersonalFilter');
                    const url = new URL(window.location.href);
                    url.searchParams.set('onlyPersonal', checkbox.checked);
                    window.location.href = url.toString();
                }
            </script>

            <a href="/app/notes/create">
                <button class="btn">Creeaza o noua nota</button>
            </a>
        </div>

    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Titlu</th>
                <th>Autor</th>
                <th>Data crearii</th>
                <th>Data actualizarii</th>
                <th>Actiuni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($notes as $note) { ?>
                <tr>
                    <td><?= $note->id ?></td>
                    <td><?= $note->title ?></td>
                    <td><?= $note->user->name ?></td>
                    <td><?= $note->createdAt ?></td>
                    <td><?= $note->updatedAt ?></td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="/app/notes/<?= $note->id ?>">
                                <button class="btn">Vezi</button>
                            </a>
                            <form action="/app/notes/<?= $note->id ?>" method="post">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-danger" type="submit">Sterge</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">
                    <div style="display: flex; justify-content: center;">
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <a
                                href="/app/notes?page=<?= $pagination['currentPage'] - 1 ?>&onlyPersonal=<?= Request::getInstance()->get('onlyPersonal') ?>">
                                <button class="btn" <?= $pagination['currentPage'] > 1 ? '' : 'disabled' ?>>
                                    <</button>
                            </a>

                            <?php for ($i = 1; $i <= $pagination['totalPages']; $i++) { ?>
                                <a href="/app/notes?page=<?= $i ?>&onlyPersonal=<?= Request::getInstance()->get('onlyPersonal') ?>">
                                    <button class="btn"><?= $i ?></button>
                                </a>
                            <?php } ?>

                            <a href="/app/notes?page=<?= $pagination['currentPage'] + 1 ?>&onlyPersonal=<?= Request::getInstance()->get('onlyPersonal') ?>">
                                <button class="btn" <?= $pagination['currentPage'] < $pagination['totalPages'] ? '' : 'disabled' ?>>></button>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
</div>