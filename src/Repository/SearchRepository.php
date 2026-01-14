<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class SearchRepository
{
    public function __construct(
        private PDO $db
    ) {}

    public function search(array $filters): array
    {
        $sql = 'SELECT id, first_name, last_name, city, university, faculty, enrollment_year
                FROM users
                WHERE 1=1';

        $params = [];

        if (!empty($filters['name'])) {
            $sql .= ' AND (first_name ILIKE :name OR last_name ILIKE :name)';
            $params['name'] = '%' . $filters['name'] . '%';
        }

        if (!empty($filters['city'])) {
            $sql .= ' AND city = :city';
            $params['city'] = $filters['city'];
        }

        if (!empty($filters['university'])) {
            $sql .= ' AND university = :university';
            $params['university'] = $filters['university'];
        }

        if (!empty($filters['faculty'])) {
            $sql .= ' AND faculty = :faculty';
            $params['faculty'] = $filters['faculty'];
        }

        if (!empty($filters['year'])) {
            $sql .= ' AND enrollment_year = :year';
            $params['year'] = (int)$filters['year'];
        }

        $sql .= ' ORDER BY last_name, first_name LIMIT 100';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
