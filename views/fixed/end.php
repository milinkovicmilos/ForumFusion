    <script>const HOST = "<?= BASE_HOST ?>"</script>
    <script src="assets/js/main.js"></script>
    <?php if ($page == "forum"): ?>
        <script src="assets/js/forum.js"></script>
    <?php endif ?>
    <?php if ($page == "post" && isLoggedIn()): ?>
        <script src="assets/js/comments.js"></script>
        <script src="assets/js/post.js"></script>
    <?php endif ?>
    <?php if ($page == "admin" && isAdmin()): ?>
        <script src="assets/admin/admin.js"></script>
    <?php endif ?>
</body>
</html>