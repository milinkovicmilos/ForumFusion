<?php require_once("models/forums/functions.php"); ?>
<?php require_once("models/categories/functions.php"); ?>
<main>
    <section id="category-info">
        <?= showCategoryInfo($_GET["categoryId"]); ?>
    </section>
    <section id="forums">
        <div class="container">
            <div class="flex-container cnt-between">
                <?= showForumsInCategory($_GET["categoryId"]); ?>
            </div>
        </div>
    </section>
</main>