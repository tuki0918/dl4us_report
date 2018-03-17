<?php

namespace App\Core;

class DataSetEntity implements EntityInterface
{
    private $title;
    private $link;
    private $description = '';
    private $dc_creator = '';
    private $dc_date;
    private $bookmark_count = 0;
    private $date_per_count = 0;

    private function __construct(
        string $title,
        string $link,
        string $description,
        string $dc_creator,
        string $dc_date,
        int $bookmark_count,
        int $date_per_count
    ) {
        $this->title = $title;
        $this->link = $link;
        $this->description = $description;
        $this->dc_creator = $dc_creator;
        $this->dc_date = $dc_date;
        $this->bookmark_count = $bookmark_count;
        $this->date_per_count = $date_per_count;
    }

    public static function create(
        string $title,
        string $link,
        string $description,
        string $dc_creator,
        string $dc_date,
        int $bookmark_count,
        int $date_per_count = 0
    ) {
        return new self(
            $title,
            $link,
            $description,
            $dc_creator,
            $dc_date,
            $bookmark_count,
            $date_per_count
        );
    }


    public function title(): string
    {
        return $this->title;
    }

    public function link(): string
    {
        return $this->link;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function creator(): string
    {
        return $this->dc_creator;
    }

    public function date(): string
    {
        $format = 'Y/m/d H:i:s';
        return date($format, strtotime($this->dc_date));
    }

    public function bookmarkCount(): int
    {
        return $this->bookmark_count;
    }

    public function datePerCount(): int
    {
        return $this->date_per_count;
    }

    public function setDatePerCount(int $count): void
    {
        $this->date_per_count = $count;
    }
}
