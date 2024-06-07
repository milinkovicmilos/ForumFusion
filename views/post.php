<?php include_once("models/forums/functions.php"); ?>
<?php include_once("models/posts/functions.php"); ?>
<main>
    <section id="forum-info">
        <div class="container">
            <?= forumInfo(getForumId($_GET["postId"])); ?>
        </div>
    </section>
    <section id="post">
        <div class="container">
            <?= showPost($_GET["postId"]) ?>
        </div>
    </section>
</main>