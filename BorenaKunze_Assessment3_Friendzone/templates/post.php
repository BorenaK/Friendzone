<?php

use model\Post;
use model\User;

function renderPost(Post $post, User $poster, bool $showDelete = false) {
    ?>
    <article class="card post">
        <div class="card-body">
            <?php echo $post->text?>
        </div>
        <div class="card-footer text-muted">
            <span>Posted by </span>
            <a href="/user/<?php echo $poster->id?>"> <?php echo $poster->name?></a>
            <span> on </span>
            <?php echo date('l jS \of F Y h:i:s A', $post->creationDate); ?>
            <?php if ($showDelete) { ?>
            <span> - </span>
            <a href="/post/<?php echo $post->id ?>/delete" class="link-danger">Delete</a>
            <?php } ?>
        </div>
    </article>
<?php
}
?>