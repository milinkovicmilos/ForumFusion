<?php include_once("models/forums/functions.php"); ?>
<?php include_once("models/posts/functions.php"); ?>
<?php if (!forumExists($_GET["forumId"])) redirect("index"); ?>
<main>
    <section id="info">
        <div class="container">
            <?= forumInfo($_GET["forumId"]); ?>
        </div>
    </section>
    <section id="posts">
        <div class="container">
            <?= showPosts($_GET["forumId"]); ?>
        </div>
    </section>
</main>