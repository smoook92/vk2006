<?php
declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Repository\UserRepository;

final class SearchController
{
    public function __construct(
        private UserRepository $users
    ) {}

    public function form(Request $r): Response
    {
        ob_start();
        require ROOT_PATH . '/templates/search/form.php';
        return new Response(ob_get_clean());
    }

    public function results(Request $r): Response
    {
        $criteria = array_filter([
            'name'            => trim($r->post('name')),
            'city'            => trim($r->post('city')),
            'university'      => trim($r->post('university')),
            'faculty'         => trim($r->post('faculty')),
            'enrollment_year' => trim($r->post('enrollment_year')),
        ]);


        $results = $this->users->search($criteria);

        ob_start();
        require ROOT_PATH . '/templates/search/results.php';
        return new Response(ob_get_clean());
    }
}
