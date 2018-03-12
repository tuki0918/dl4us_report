<?php

require_once __DIR__.'/MyFeedEntity.php';
require_once __DIR__.'/MyFeedParser.php';
require_once __DIR__.'/MyFeedStorage.php';

class MyFeedSpider
{
    private const FEED_PARTITION_SIZE = 20;

    private $user;
    private $currentIndex = 0;

    public $storage;

    public function __construct(
        string $user,
        MyFeedStorage $storage,
        int $currentIndex = 0
    ) {
        $this->user = $user;
        $this->currentIndex = $currentIndex;

        $this->storage = $storage;
    }

    private function countUpIndex(): void
    {
        $this->currentIndex++;
    }

    public function storage(): MyFeedStorage
    {
        return $this->storage;
    }

    public static function createRecentUrl(
        string $user,
        int $index = 0
    ) : string {
        // オフセットを計算する
        $offset = self::FEED_PARTITION_SIZE * $index;

        // 取得するフィードのURLを指定
        $feed = "http://b.hatena.ne.jp/${user}/rss?of=${offset}";

        // キャッシュを防止するため、パラメータを追加する
        $param = explode('?' , $feed) ;
        $feed .= (isset($param[1]) && !empty($param[1])) ? '&' . time() : '?' . time() ;

        return $feed;
    }

    public function crawling(int $sleepTime = 1, $recursive = true)
    {
        try {
            $xml = $this->request();
            $entities = MyFeedParser::parse($xml);

            $this->countUpIndex();
        } catch (Throwable | Exception $e) {
            var_dump($e);
            // 待機し、再リクエスト
            sleep(10);
            $this->crawling($sleepTime, $recursive);
            return;
        }

        foreach ($entities as $entity) {
            $this->storage()->save($entity);
        }

        if (!$recursive) {
            return;
        }

        // 待機
        sleep($sleepTime);

        // 再帰クローリング
        if (!empty($entities)) {
            $this->crawling($sleepTime, $recursive);
        }
    }

    private function request(): string
    {
        $index = $this->currentIndex;
        $endpoint = self::createRecentUrl($this->user, $index);

        echo "[INFO] ${index}: ${endpoint} リクエスト中...。\n";

        // hatena domain tips
        $context = stream_context_create([
            'http' => [
                'header' => 'User-Agent: tuki0918\'s crawler',
            ],
        ]);

        $xml = @file_get_contents($endpoint, false, $context);

        if (!$xml) {
            new Exception('リクエストエラー : MyFeedSpider');
        }

        return $xml;
    }
}
