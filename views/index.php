<main>
    <section id="banner">
        <?php if (checkFlash("FORUM")) : ?>
            <div class="alert">
                <?= getFlash("FORUM") ?>
            </div>
        <?php endif ?>
        <div class="container">
            <h2>Where Communities Converge</h2>
            <p>Join the conversation, create your own forums, and connect with people around the world.</p>
            <a href="index.php?page=createforum" class="button">Create a Forum</a>
        </div>
    </section>
    <?php include_once("models/forums/functions.php"); ?>
    <section id="popular">
        <h2>Popular Forums</h2>
        <div class="container">
            <div class="flex-container cnt-evenly stretch">
                <?= popularForums(); ?>
            </div>
        </div>
    </section>
    <section id="categories">
        <h2>Forum categories</h2>
        <div class="container">
            <div class="flex-container cnt-evenly stretch">
                <?= forumCategories(); ?>
            </div>
        </div>
    </section>
</main>