<?php

?>

<div class="container">
    <div style="display: flex; flex-direction: column; gap: 1rem; padding: 1rem;">
            <h1>Sesiuni active (mobil)</h1>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Expira la</th>
                        <th>Ultima utilizare</th>
                        <th>Creat la</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accessTokens as $accessToken) { ?>
                        <tr style="<?= strtotime($accessToken->expiresAt) < time() ? 'opacity: 0.5;' : '' ?>">
                            <td><?= $accessToken->id ?></td>
                            <td><?= $accessToken->expiresAt ?></td>
                            <td><?= $accessToken->lastUsedAt ?></td>
                            <td><?= $accessToken->createdAt ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">
                            <div style="display: flex; justify-content: center;">
                                <div style="display: flex; gap: 0.5rem; align-items: center;">
                                    <a
                                        href="/app/sessions?page=<?= $pagination['currentPage'] - 1 ?>">
                                        <button class="btn" <?= $pagination['currentPage'] > 1 ? '' : 'disabled' ?>><</button>
                                    </a>

                                    <?php for ($i = 1; $i <= $pagination['totalPages']; $i++) { ?>
                                        <a
                                            href="/app/sessions?page=<?= $i ?>">
                                            <button class="btn"><?= $i ?></button>
                                        </a>
                                    <?php } ?>

                                    <a
                                        href="/app/sessions?page=<?= $pagination['currentPage'] + 1 ?>">
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
</div>