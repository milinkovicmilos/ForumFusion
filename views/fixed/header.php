<header>
    <div class="container">
        <nav>
            <ul class="flex-container">
                <li><a href="index.php" class="nav-link"><h1>ForumFusion</h1></a></li>
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="#" class="nav-link">My Forums</a></li>
                <li><a href="#" class="nav-link">Notifications</a></li>
                <?php if (isLoggedIn()): ?>
                    <li class="end">
                        <?= $_SESSION["USER"]->first_name . " " . $_SESSION["USER"]->last_name . " >> "?>
                        <a href="models/users/logout.php" class="nav-link">Log Out</a>
                    </li>
                <?php else: ?>
                    <li class="end"><a href="index.php?page=login" class="nav-link">Log In</a></li>
                <?php endif ?>
            </ul>
        </nav>
    </div>
</header>