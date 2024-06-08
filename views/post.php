<?php include_once("models/forums/functions.php"); ?>
<?php include_once("models/posts/functions.php"); ?>
<?php include_once("models/comments/functions.php"); ?>
<?php if (!postExists($_GET["postId"])) redirect("index"); ?>
<main>
    <section id="forum-info">
        <div class="container">
            <?= forumInfo(getForumId($_GET["postId"])); ?>
        </div>
    </section>
    <section id="post">
        <div class="container">
            <?= showPost($_GET["postId"]); ?>
        </div>
    </section>
    <section id="comments">
        <div class="container">
            <?php if (!isLoggedIn()): ?>
                <b><a href='index.php?page=login'>Log in</a> to add a comment!</b>
            <?php else: ?>
                <form>
                    <label for="comment-area">Leave a comment:</label>
                    <textarea name="comment" id="comment-area"></textarea>
                    <input type="submit" value="Send">
                </form>
            <?php endif ?>
        </div>
        <div id="comments-container" class="container">
            <h2>Comments</h2>
            <?= showPostComments($_GET["postId"]); ?>
        </div>
    </section>
</main>