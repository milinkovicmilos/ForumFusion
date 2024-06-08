<?php

require_once("../../config/connection.php");
require_once("../common.php");
require_once("functions.php");
require_once("../posts/functions.php");

if (!isLoggedIn()) {
    http_response_code(401);
}

$data = getJSON();
if (!isset($data->postId) || empty($data->postId) || !validatePostId($data->postId)) {
    responseCodeEnd(400);
}

if (!isset($data->text) || empty($data->text) || !validateText($data->text)) {
    responseCodeEnd(400);
}

// Write to database
$result = addComment(getLoggedInUser()->id, $data->postId, $data->text);
if ($result) {
    http_response_code(200);
} else {
    responseCodeEnd(500);
}

$response = [
    "commentId" => $result,
    "username" => getLoggedInUser()->username,
    "text" => $data->text
];
respondJSON($response);