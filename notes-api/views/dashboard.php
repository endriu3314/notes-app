<?php

use NotesApi\Request\Request;

?>

<div>
    <h1>Welcome back, <?= Request::getInstance()->user()->name ?></h1>
</div> 