<?php if (!isLoggedIn()) redirect("login"); ?>
<main>
    <section id="forum-creation">
        <div class="container">
            <?php if (checkFlash("FORUM")) : ?>
                <div class="alert">
                    <?= getFlash("FORUM") ?>
                </div>
            <?php endif ?>
            <h2>Create your forum</h2>
            <form action="models/forums/createforum.php" method="POST" id="cfform" class="validate validate-send">
                <div class="input-container">
                    <input type="text" name="forumname" id="forumname" placeholder="Forum name">
                </div>
                <div class="input-container">
                    <input type="text" name="forumdescription" id="forumdescription" placeholder="Forum description">
                </div>
                <div class="input-container">
                    <select name="forumcategory" id="forumcategory">
                        <option value="0">Choose ...</option>
                        <?php
                            include_once("models/forums/functions.php");
                            echo categoryOptions();
                        ?>
                    </select>
                </div>
                <input type="submit" value="Create">
            </form>
        </div>
    </section>
</main>