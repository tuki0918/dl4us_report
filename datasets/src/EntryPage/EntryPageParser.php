<?php

namespace App\EntryPage;

use \stdClass;
use \DateTime;
use \Exception;

class EntryPageParser
{
    public static function parse(string $json): int
    {
        $feed = self::json_parse($json);

        $date_per_count = -1;
        $bookmarks = $feed->bookmarks ?? [];
        $index = count($bookmarks);

        if ($index > 0) {
            $bookmark = $bookmarks[$index-1];

            $day1 = new DateTime($bookmark->timestamp);
            $day2 = new DateTime();
            $days = $day1->diff($day2)->days;

            if ($days > 0) {
                $date_per_count = round($feed->count/$days);
            } else {
                $date_per_count = $feed->count;
            }
        }

        return $date_per_count;
    }

    private static function json_parse(string $json): stdClass
    {
        // 取得したJSONをオブジェクトに変換する
        $feed = @json_decode($json);

        if (!$feed
            || !isset($feed->count)
            || !isset($feed->bookmarks)
            || !isset($feed->url)
            || !isset($feed->eid)
            || !isset($feed->title)
            || !isset($feed->screenshot)
            || !isset($feed->entry_url)
        ) {
            new Exception('パースエラー : EntryPageParser');
        }

        return $feed;
    }
}
