<header>
    <div class="container">
        <nav>
            <ul class="flex-container">
                <li><a href="#" class="nav-link"><h1>ForumFusion</h1></a></li>
                <li><a href="#" class="nav-link">Home</a></li>
                <li><a href="#" class="nav-link">My Forums</a></li>
                <li><a href="#" class="nav-link">Notifications</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="models/users/logout.php" class="nav-link end">Log Out</a></li>
                <?php else: ?>
                    <li class="end"><a href="index.php?page=login" class="nav-link">Log In</a></li>
                <?php endif ?>
            </ul>
        </nav>
    </div>
</header>