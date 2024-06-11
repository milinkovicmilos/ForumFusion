<?php

function getPageAccessLog() : array {
    $fileRecords = file(PAGE_ACCESS_LOG_PATH);
    $arr = [];
    foreach ($fileRecords as $record) {
        $infos = explode(LOG_SEPARATOR, $record);
        $obj = new stdClass();
        $obj->username = $infos[0];
        $pageInfo = explode(PAGE_ID_SEPARATOR, $infos[1]);
        $obj->page = $pageInfo[0];
        if (str_contains($infos[1], PAGE_ID_SEPARATOR)) {
            $obj->pageId = $pageInfo[1];
        }
        $arr[] = $obj;
    }
    return $arr;
}