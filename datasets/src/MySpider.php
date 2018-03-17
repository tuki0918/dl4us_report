<?php

namespace App;

use App\Core\DataSetEntity;
use App\Core\StorageInterface;
use App\EntryPage\EntryPageRequest;
use App\MyFeed\MyFeedRequest;

use \Exception;
use \Throwable;

class MySpider
{
    private $user;
    private $currentIndex = 0;

    public $storage;

    public function __construct(
        string $user,
        StorageInterface $storage,
        int $currentIndex = 0
    ) {
        $this->user = $user;
        $this->currentIndex = $currentIndex;

        $this->storage = $storage;
    }

    private function user(): string
    {
        return $this->user;
    }

    private function currentIndex(): int
    {
        return $this->currentIndex;
    }

    /**
     * カウントアップ
     */
    private function currentIndexNext(): void
    {
        $this->currentIndex++;
    }

    /**
     * @return StorageInterface
     */
    public function storage(): StorageInterface
    {
        return $this->storage;
    }

    /**
     * @param int $sleepTime
     * @param bool $recursive
     */
    public function crawling(int $sleepTime = 1, $recursive = true)
    {
        try {
            $entities = MyFeedRequest::request($this->user(), $this->currentIndex());
            $this->currentIndexNext();
        } catch (Throwable | Exception $e) {
            var_dump($e);
            // 待機し、再リクエスト
            sleep(10);
            $this->crawling($sleepTime, $recursive);
            return;
        }

        /** @var DataSetEntity $entity */
        foreach ($entities as $entity) {

            try {
                sleep(3);
                $date_per_count = EntryPageRequest::request($entity->link());
            } catch (Throwable | Exception $e) {
                var_dump($e);
                $date_per_count = -9;
            }

            $entity->setDatePerCount($date_per_count);
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
}
