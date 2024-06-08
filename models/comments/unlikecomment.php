<?php

require_once("../../config/connection.php");
require_once("../common.php");
require_once("functions.php");

if (!isPost() || !isLoggedIn()) {
    responseCodeEnd(401);
}

$data = getJSON();
if (!isset($data->commentId) || empty($data->commentId) || !validateCommentId($data->commentId)) {
    responseCodeEnd(400);
}

if (!isLiked($data->commentId)) {
    responseCodeEnd(403);
}

if (unlikeComment($data->commentId)) {
    responseCodeEnd(200);
} else {
    responseCodeEnd(500);
}