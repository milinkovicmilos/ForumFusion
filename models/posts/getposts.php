<?php

require_once("../../config/connection.php");
require_once("../common.php");
require_once("functions.php");

if (!isPost()) {
    responseCodeEnd(401);
}

$data = getJSON();
if (!is_string($data->search)) {
    responseCodeEnd(400);
}

if (!isset($data->forumId) || empty($data->forumId) || !is_numeric($data->forumId)) {
    responseCodeEnd(400);
}

$validSort = [];
foreach (SORT_MAP as $key => $value) {
    $validSort[] = $key;
}

if (!isset($data->sort) || empty($data->sort) || 
    !is_numeric($data->sort) || !in_array($data->sort, $validSort)) {
    responseCodeEnd(400);
}

$validPerPage = [];
foreach (PERPAGE_MAP as $key => $value) {
    $validPerPage[] = $key;
}
if (!isset($data->perPage) || empty($data->perPage) || 
    !is_numeric($data->perPage) || !in_array($data->perPage, $validPerPage)) {
    responseCodeEnd(400);
}

$posts = getPosts($data->forumId, $data->search, $data->sort, $data->perPage);
respondJSON($posts);