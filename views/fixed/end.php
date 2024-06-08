    <script>const HOST = "<?= BASE_HOST ?>"</script>
    <script src="assets/js/main.js"></script>
    <?php if ($page == "post" && isLoggedIn()): ?>
        <script src="assets/js/comments.js"></script>
    <?php endif ?>
</body>
</html>