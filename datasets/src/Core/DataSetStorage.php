<?php

namespace App\Core;

use \SplFileObject;

class DataSetStorage implements StorageInterface
{
    private $storage = [];

    public function __construct(
        DataSetEntity ...$data
    ) {
        $this->storage = $data;
    }

    public function save(DataSetEntity $entity): void
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
            'URL',
            'TITLE',
            'SCHEME',
            'HOST',
            'PATH',
            'QUERY',
            'FRAGMENT',
            'BOOKMARK_COUNT',
            'DATE_PER_COUNT',
        ];

        $items = $this->storage();

        /** @var DataSetEntity $item */
        foreach ($items as $item) {

            $link = $item->link();

            $data[] = [
                $item->date(),
                $link,
                mb_substr($item->title(), 0, 50),
                parse_url($link, PHP_URL_SCHEME),
                parse_url($link, PHP_URL_HOST),
                parse_url($link, PHP_URL_PATH),
                parse_url($link, PHP_URL_QUERY),
                parse_url($link, PHP_URL_FRAGMENT),
                $item->bookmarkCount(),
                $item->datePerCount(),
            ];
        }

        foreach ($data as $row) {
            $file->fputcsv($row);
        }
    }
}
