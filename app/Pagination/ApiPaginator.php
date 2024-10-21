<?php

namespace App\Pagination;

use Illuminate\Pagination\LengthAwarePaginator;

class ApiPaginator extends LengthAwarePaginator
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'data' => $this->items->toArray(),
            'pagination' => [
                'totalItem'         => $this->total(),
                'perPage'           => $this->perPage(),
                'currentPage'       => $this->currentPage(),
                'totalPage'         => $this->lastPage(),
                'nextPageUrl'       => $this->nextPageUrl(),
                'previousPageUrl'   => $this->previousPageUrl(),
            ],
        ];
    }
}
