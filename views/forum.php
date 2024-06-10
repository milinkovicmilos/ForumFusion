<?php include_once("models/forums/functions.php"); ?>
<?php include_once("models/posts/functions.php"); ?>
<?php if (!forumExists($_GET["forumId"])) redirect("index"); ?>
<main>
    <section id="info">
        <div class="container">
            <?= forumInfo($_GET["forumId"]); ?>
        </div>
    </section>
    <section id="add-post">
        <a href="index.php?page=createpost&forumId=<?= $_GET["forumId"] ?>" class="button">
            Add new post
        </a>
    </section>
    <section id="search">
        <div class="container">
            <form class="flex-container cnt-between">
                <input type="text" name="search" id="" placeholder="Search...">
                <div>
                    <label for="sort">Sort By :</label>
                    <select name="sort" id="sort">
                        <option value="1">Newest</option>
                        <option value="2">Oldest</option>
                        <option value="3">Most Liked</option>
                        <option value="4">Least Liked</option>
                    </select>
                </div>
                <div>
                    <label for="perPage">Per page</label>
                    <select name="perPage" id="perPage">
                        <option value="1">5</option>
                        <option value="2">10</option>
                        <option value="3">15</option>
                    </select>
                </div>
                <div id="filters">
                    <details>
                        <?= forumTags($_GET["forumId"]); ?>
                    </details>
                </div>
                <input type="submit" value="Search">
            </form>
        </div>
    </section>
    <section id="posts">
        <div class="container"></div>
    </section>
</main>