<?php

namespace App\Core;

interface StorageInterface
{
    /**
     * @param DataSetEntity $entity
     */
    public function save(DataSetEntity $entity): void;

    /**
     * @return int
     */
    public function count(): int;

    /**
     * @return array
     */
    public function storage(): array;

    /**
     * @param string $path
     */
    public function export(string $path): void;
}
