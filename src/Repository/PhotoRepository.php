<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class PhotoRepository
{
    public function __construct(
        private PDO $db
    ) {}

    public function createAlbum(int $userId, string $title): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO photo_albums (user_id, title)
             VALUES (:u, :t)
             RETURNING id'
        );

        $stmt->execute([
            'u' => $userId,
            't' => $title,
        ]);

        return (int)$stmt->fetchColumn();
    }

    public function albumsOf(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM photo_albums
             WHERE user_id = :u
             ORDER BY created_at'
        );

        $stmt->execute(['u' => $userId]);
        return $stmt->fetchAll();
    }

    public function addPhoto(
        int $userId,
        int $albumId,
        string $path,
        bool $isAvatar
    ): void {
        if ($isAvatar) {
            $this->db->prepare(
                'UPDATE photos SET is_avatar = false WHERE user_id = :u'
            )->execute(['u' => $userId]);
        }

        $stmt = $this->db->prepare(
            'INSERT INTO photos (album_id, user_id, path, is_avatar)
             VALUES (:a, :u, :p, :av)'
        );

        $stmt->execute([
            'a'  => $albumId,
            'u'  => $userId,
            'p'  => $path,
            'av' => $isAvatar,
        ]);
    }

    public function setAvatar(int $userId, string $path): void
    {
        // снять старый аватар
        $this->db->prepare(
            'UPDATE photos SET is_avatar = false WHERE user_id = :u'
        )->execute(['u' => $userId]);

        // добавить новый
        $this->db->prepare(
            'INSERT INTO photos (user_id, path, is_avatar)
            VALUES (:u, :p, true)'
        )->execute([
            'u' => $userId,
            'p' => $path,
        ]);
    }


    public function avatarOf(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM photos
            WHERE user_id = :u AND is_avatar = true
            LIMIT 1'
        );
        $stmt->execute(['u' => $userId]);

        return $stmt->fetch() ?: null;
    }


    public function photosInAlbum(int $albumId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM photos
             WHERE album_id = :a
             ORDER BY created_at'
        );

        $stmt->execute(['a' => $albumId]);
        return $stmt->fetchAll();
    }
}
