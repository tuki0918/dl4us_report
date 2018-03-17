<?php

date_default_timezone_set('Asia/Tokyo');

require_once __DIR__.'/vendor/autoload.php';

use App\Core\DataSetStorage;
use App\MySpider;


const USER_ID = 'tuki0918';
const SLEEP_TIME = 5;
const EXPORT_TSV_FILE = __DIR__ . '/../__dataset__.tsv';

$spider = new MySpider(USER_ID, new DataSetStorage());

// クローリング
$spider->crawling(SLEEP_TIME);

// 取得したデータをTSV形式で出力する
$spider->storage()->export(EXPORT_TSV_FILE);

// 出力パスを確認
var_dump(EXPORT_TSV_FILE);

exit();
