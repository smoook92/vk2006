<?php
declare(strict_types=1);

namespace App\Service;

use App\Repository\SearchRepository;

final class SearchService
{
    public function __construct(
        private SearchRepository $search
    ) {}

    public function find(array $query): array
    {
        return $this->search->search([
            'name'       => trim($query['name'] ?? ''),
            'city'       => trim($query['city'] ?? ''),
            'university' => trim($query['university'] ?? ''),
            'faculty'    => trim($query['faculty'] ?? ''),
            'year'       => trim($query['year'] ?? ''),
        ]);
    }
}
