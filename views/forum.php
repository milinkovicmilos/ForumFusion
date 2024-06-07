<main>
    <?php include_once("models/forums/functions.php"); ?>
    <?php include_once("models/posts/functions.php"); ?>
    <section id="info">
        <div class="container">
            <?= forumInfo($_GET["forumId"]); ?>
        </div>
    </section>
    <section id="posts">
        <div class="container">
            <?= getPosts($_GET["forumId"]); ?>
        </div>
    </section>
</main>