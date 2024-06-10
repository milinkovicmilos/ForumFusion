<?php include_once("models/forums/functions.php"); ?>
<?php if (!isLoggedIn()) redirect("login"); ?>
<main>
    <section id="forum-info">
        <div class="container">
            <?= forumInfo($_GET["forumId"]); ?>
        </div>
    </section>
    <section id="post-form">
        <form action="models/posts/addpost.php" method="POST" enctype="multipart/form-data" class="validate validate-send">
            <div class="input-container">
                <input type="text" name="title" id="post-title" placeholder="Post title">
            </div>
            <div class="input-container">
                <textarea name="text" id="post-text" placeholder="Post text"></textarea>
            </div>
            <div class="input-container">
                <input type="file" name="image" id="post-image">
            </div>
            <div class="input-container">
                <details>
                    <?= forumTags($_GET["forumId"]); ?>
                </details>
            </div>
            <input type="hidden" name="forumId" value="<?= $_GET["forumId"] ?>">
            <input type="submit" value="Post">
        </form>
    </section>
</main>