<?php

use model\Post;
use model\User;

require_once($_SERVER['DOCUMENT_ROOT'] . "/templates/user.php");

function renderUserPage(User $user, array $posts, bool $isSelf) {
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

        <title>User 5</title>
        <link href="/index.css" rel="stylesheet">
        <style>
            .post {
                margin: 1rem 0;
            }
        </style>
    </head>
    <body>
    <?php include "header.php"; ?>

    <div id="main-container">
        <main>
            <?php renderUser($user, $posts, $isSelf) ?>
        </main>
    </div>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>


    </body>
    </html>
    <?php
}
?>
