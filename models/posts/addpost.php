<?php

require_once("../../config/connection.php");
require_once("../common.php");
require_once("functions.php");
require_once("../forums/functions.php");

if (!isPost() || !isLoggedIn()) {
    redirect("index");
}

$forumId = post("forumId");
$title = post("title");
$text = post("text");
$image = $_FILES["image"]["tmp_name"];
$tags = post("tag");

if (!isset($forumId) || !forumExists($forumId) ||
    !isset($title) || !validatePostTitle($title) ||
    !isset($text) || !validatePostText($text)) {
        redirect("index");
}

if (isset($tags) && !validatePostTags($tags, $forumId)) {
    redirect("index");
}

if (isset($image) && !empty($image)) {
    $image = handlePostImage($image);
}

$postId = addPost($forumId, $title, $text, $image, $tags);
redirect("post", "postId=$postId");