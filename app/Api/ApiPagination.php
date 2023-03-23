<?php

namespace App\Api;

class ApiPagination
{
    /**
     * @var int
     */
    public int $currentPage = 1;

    /**
     * @var int
     */
    public int $perPage = 0;

    /**
     * @var int
     */
    public int $total = 0;

    /**
     * @var int
     */
    public int $totalPages = 0;

    public function __construct(int $page, int $perPage, int $totalOfElementsToPaginate = 0, int $maxPage = 0)
    {
        $this->currentPage = $page;
        $this->perPage = $perPage;
        if ($totalOfElementsToPaginate !== 0) {
            $this->total = $totalOfElementsToPaginate;
        }
        if ($maxPage !== 0) {
            $this->totalPages = $maxPage;
        }
    }
}
