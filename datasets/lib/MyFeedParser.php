<?php

require_once __DIR__.'/MyFeedEntity.php';

class MyFeedParser
{
    public static function parse(string $xml): array
    {
        $feed = self::xml_parse($xml);

        $data = [];
        foreach ($feed->item as $item) {
            $data[] = MyFeedEntity::create(
                (string)$item->title,
                (string)$item->link,
                '',
                (string)$item->children('dc', true)->creator,
                (string)$item->children('dc', true)->date,
                (int)$item->children('hatena', true)->bookmarkcount
            );
        }

        return $data;
    }

    private static function xml_parse(string $xml): object
    {
        // フィードを取得してオブジェクトに変換
        $feed = simplexml_load_string($xml);

        if (!$feed
            || !isset($feed->item) || empty($feed->item)
            || !isset($feed->channel) || empty($feed->channel)
        ) {
            new Exception('パースエラー : MyFeedParser');
        }

        return $feed;
    }
}
