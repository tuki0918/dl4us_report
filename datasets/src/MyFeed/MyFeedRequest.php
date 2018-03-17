<?php

namespace App\MyFeed;

use \Exception;

class MyFeedRequest
{
    private const FEED_PARTITION_SIZE = 20;

    /**
     * @param string $user
     * @param int $index
     * @return string
     */
    public static function createEndpoint(string $user, int $index = 0) : string
    {
        // オフセットを計算する
        $offset = self::FEED_PARTITION_SIZE * $index;

        // 取得するフィードのURLを指定
        $feed = "http://b.hatena.ne.jp/${user}/rss?of=${offset}";

        // キャッシュを防止するため、パラメータを追加する
        $param = explode('?' , $feed) ;
        $feed .= (isset($param[1]) && !empty($param[1])) ? '&' . time() : '?' . time() ;

        return $feed;
    }

    /**
     * @param string $user
     * @param int $index
     * @return string
     */
    private static function _request(string $user, int $index): string
    {
        $endpoint = self::createEndpoint($user, $index);

        echo "[INFO] ${index}: ${endpoint} リクエスト中...。\n";

        // hatena domain tips
        $context = stream_context_create([
            'http' => [
                'header' => 'User-Agent: my crawler',
            ],
        ]);

        $xml = @file_get_contents($endpoint, false, $context);

        if (!$xml) {
            new Exception('リクエストエラー : MyFeedRequest');
        }

        return $xml;
    }

    /**
     * @param string $user
     * @param int $index
     * @return array
     */
    public static function request(string $user, int $index): array
    {
        $data = self::_request($user, $index);

        return MyFeedParser::parse($data);
    }
}
