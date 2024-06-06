<main>
    <section id="login">
        <?php if (checkFlash("REGISTER")): ?>
            <div class="info">
                <?= getFlash("REGISTER") ?>
            </div>
        <?php endif ?>
        <?php if (checkFlash("USERNAME")): ?>
            <div class="alert">
                <?= getFlash("USERNAME") ?>
            </div>
        <?php endif ?>
        <?php if (checkFlash("EMAIL")): ?>
            <div class="alert">
                <?= getFlash("EMAIL") ?>
            </div>
        <?php endif ?>
        <div class="container">
            <div class="flex-container cnt-evenly stretch">
                <div class="card">
                    <h2>Register</h2>
                    <form action="models/users/register.php" method="POST" class="validate">
                        <div class="input-container">
                            <input type="text" name="firstname" id="firstname" placeholder="First Name">
                        </div>
                        <div class="input-container">
                            <input type="text" name="lastname" id="lastname" placeholder="Last Name">
                        </div>
                        <div class="input-container">
                            <input type="text" name="username" id="username" placeholder="Username">
                        </div>
                        <div class="input-container">
                            <input type="text" name="email" id="email" placeholder="Email">
                        </div>
                        <div class="input-container">
                            <input type="password" name="password" id="password" placeholder="Password">
                        </div>
                        <div class="input-container">
                            <input type="password" name="repassword" id="repassword" placeholder="Repeat Password">
                        </div>
                        <input type="submit" value="Register">
                    </form>
                </div>
                <div class="card">
                    <h2>Log In</h2>
                    <form action="models/users/login.php" method="POST" class="validate">
                        <input type="text" name="username" id="reqUsername" placeholder="Username">
                        <input type="password" name="password" id="reqPassword" placeholder="Password">
                        <input type="submit" value="Log In">
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>