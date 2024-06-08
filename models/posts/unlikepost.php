<?php

require_once("../../config/connection.php");
require_once("../common.php");
require_once("functions.php");

if (!isPost() || !isLoggedIn()) {
    responseCodeEnd(401);
}

$data = getJSON();
if (!isset($data->postId) || empty($data->postId) || !validatePostId($data->postId)) {
    responseCodeEnd(400);
}

if (!isPostLiked($data->postId)) {
    responseCodeEnd(403);
}

if (unlikePost($data->postId)) {
    responseCodeEnd(200);
} else {
    responseCodeEnd(500);
}