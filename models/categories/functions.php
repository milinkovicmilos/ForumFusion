<?php

function forumCategories() {
    $query = "
        SELECT id, name
        FROM categories
    ";
    $categories = queryPrepared($query, []);
    $html = "";
    foreach ($categories as $category) {
        $html .= "
            <a class='reset-link index-item' href='index.php?page=category&categoryId=$category->id'>
                <div>
                    <h3>$category->name</h3>
                </div>
            </a>
        ";
    }
    return $html;
}

function showCategoryInfo($categoryId) : string {
    $query = "
        SELECT name
        FROM categories
        WHERE id = :cid
    ";
    $results = queryPrepared($query, ["cid" => $categoryId]);
    if (empty($results)) {
        redirect("index");
    }
    $name = $results[0]->name;
    return "<h2>Category name : $name</h2>";
}