<?php

require_once __DIR__.'/lib/MyFeedSpider.php';
require_once __DIR__.'/lib/MyFeedStorage.php';

const USER_ID = 'tuki0918';
const SLEEP_TIME = 3;
const EXPORT_TSV_FILE = __DIR__ . '/dataset.tsv';

$storage = new MyFeedStorage();
$spider = new MyFeedSpider(USER_ID, $storage);

// クローリング
$spider->crawling(SLEEP_TIME);

// 取得したデータをTSV形式で出力する
$spider->storage()->export(EXPORT_TSV_FILE);

// 出力パスを確認
var_dump(EXPORT_TSV_FILE);

exit();
