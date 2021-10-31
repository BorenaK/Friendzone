<?php

use model\Post;
use model\User;

function renderUser(User $user, Array $posts, bool $isSelf = false) {
    ?>
    <h1><?php echo $user->name?></h1>
    <p>
        <?php echo $user->bio?>
    </p>
    <?php if ($isSelf) { ?>
        <a href="/my/profile/edit">Edit</a>
        <a href="/create">Create Post</a>
    <?php } ?>
    <address>
        <a href="mailto:<?php echo $user->email?>"><?php echo $user->email?></a>
    </address>
    <?php
    foreach ($posts as $post) {
        renderPost($post, $user, $isSelf);
    }
}
?>
