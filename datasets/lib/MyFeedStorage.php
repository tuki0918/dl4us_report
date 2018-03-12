<?php

require_once __DIR__.'/MyFeedEntity.php';

class MyFeedStorage
{
    private $storage = [];

    public function __construct(
        MyFeedEntity ...$data
    ) {
        $this->storage = $data;
    }

    public function save(MyFeedEntity $entity): void
    {
        $this->storage[] = $entity;
    }

    public function count(): int
    {
        return count($this->storage());
    }

    public function storage(): array
    {
        return $this->storage;
    }

    public function export(string $path): void
    {
        $file = new SplFileObject($path, 'w');
        $file->setCsvControl("\t");

        $data = [];
        $data[] = [
            'DATE',
            'TITLE',
            'SCHEME',
            'HOST',
            'PATH',
            'QUERY',
            'FRAGMENT',
            'BOOKMARK_COUNT',
        ];

        $items = $this->storage();

        /** @var MyFeedEntity $item */
        foreach ($items as $item) {

            $link = $item->link();

            $data[] = [
                $item->date(),
                mb_substr($item->title(), 0, 50),
                parse_url($link, PHP_URL_SCHEME),
                parse_url($link, PHP_URL_HOST),
                parse_url($link, PHP_URL_PATH),
                parse_url($link, PHP_URL_QUERY),
                parse_url($link, PHP_URL_FRAGMENT),
                $item->bookmarkCount(),
            ];
        }

        foreach ($data as $row) {
            $file->fputcsv($row);
        }
    }
}
