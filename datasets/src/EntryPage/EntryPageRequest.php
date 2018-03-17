<?php

namespace App\EntryPage;

use \Exception;

class EntryPageRequest
{
    /**
     * @param string $url
     * @return string
     */
    public static function createEndpoint(string $url) : string
    {
        $endpoint = 'http://b.hatena.ne.jp/entry/jsonlite/?url=' . rawurlencode($url);
        return $endpoint;
    }

    /**
     * @param string $url
     * @return string
     */
    private static function _request(string $url): string
    {
        $endpoint = self::createEndpoint($url);

        echo "[INFO] entry: ${endpoint} リクエスト中...。\n";

        // hatena domain tips
        $context = stream_context_create([
            'http' => [
                'header' => 'User-Agent: my crawler',
            ],
        ]);

        $json = @file_get_contents($endpoint, false, $context);

        if (!$json) {
            new Exception('リクエストエラー : EntryRequest');
        }

        return $json;
    }

    /**
     * @param string $url
     * @return int
     */
    public static function request(string $url): int
    {
        $data = self::_request($url);

        return EntryPageParser::parse($data);
    }
}
