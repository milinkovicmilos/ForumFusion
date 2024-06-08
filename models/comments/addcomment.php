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
    http_response_code(400);
}

if (!isset($data->text) || empty($data->text) || !validateText($data->text)) {
    http_response_code(400);
}

// Write to database
if (addComment(getLoggedInUser()->id, $data->postId, $data->text)) {
    http_response_code(200);
} else {
    http_response_code(500);
}

$response = [
    "username" => getLoggedInUser()->username,
    "text" => $data->text
];
respondJSON($response);