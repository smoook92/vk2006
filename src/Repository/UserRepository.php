<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class UserRepository
{
    public function __construct(
        private PDO $db
    ) {}

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE email = :e'
        );
        $stmt->execute(['e' => $email]);

        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (
                email,
                first_name,
                last_name,
                password_hash
            ) VALUES (
                :email,
                :first_name,
                :last_name,
                :password_hash
            )
            RETURNING id'
        );

        $stmt->execute([
            'email'         => $data['email'],
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'password_hash'=> $data['password'], // ← ОБРАТИ ВНИМАНИЕ
        ]);

        return (int)$stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function updateProfile(int $userId, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET
                first_name = :first_name,
                last_name = :last_name,
                birth_date = :birth_date,
                city = :city,
                university = :university,
                faculty = :faculty,
                enrollment_year = :enrollment_year,
                about = :about,
                interests = :interests,
                updated_at = now()
            WHERE id = :id'
        );

        $stmt->execute([
            'id'               => $userId,
            'first_name'       => $data['first_name'],
            'last_name'        => $data['last_name'],
            'birth_date'       => $data['birth_date'],
            'city'             => $data['city'],
            'university'       => $data['university'],
            'faculty'          => $data['faculty'],
            'enrollment_year'  => $data['enrollment_year'],
            'about'            => $data['about'],
            'interests'        => $data['interests'],
        ]);
    }

    public function search(array $c): array
    {
        $c = array_merge([
            'name'            => '',
            'city'            => '',
            'university'      => '',
            'faculty'         => '',
            'enrollment_year' => '',
        ], $c);
        
        $sql = 'SELECT id, first_name, last_name, city, university
                FROM users
                WHERE 1=1';
        $params = [];

        if ($c['name'] !== '') {
            $sql .= ' AND (first_name ILIKE :name OR last_name ILIKE :name)';
            $params['name'] = '%' . $c['name'] . '%';
        }

        if ($c['city'] !== '') {
            $sql .= ' AND city ILIKE :city';
            $params['city'] = '%' . $c['city'] . '%';
        }

        if ($c['university'] !== '') {
            $sql .= ' AND university ILIKE :university';
            $params['university'] = '%' . $c['university'] . '%';
        }

        if ($c['faculty'] !== '') {
            $sql .= ' AND faculty ILIKE :faculty';
            $params['faculty'] = '%' . $c['faculty'] . '%';
        }

        if ($c['enrollment_year'] !== '') {
            $sql .= ' AND enrollment_year = :year';
            $params['year'] = (int)$c['enrollment_year'];
        }

        $sql .= ' ORDER BY last_name, first_name LIMIT 100';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function onlineStatus(array $user): string
    {
        if (!$user['last_seen_at']) {
            return 'давно';
        }

        $last = strtotime($user['last_seen_at']);

        if (time() - $last <= 300) {
            return 'online';
        }

        if (time() - $last <= 3600) {
            return 'был недавно';
        }

        return 'был давно';
    }

}