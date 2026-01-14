<?php
declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Repository\PhotoRepository;
use App\Service\CurrentUserService;

final class PhotoController
{
    public function __construct(
        private PhotoRepository $photos,
        private CurrentUserService $currentUser
    ) {}

    public function albums(Request $r): Response
    {
        $me = $this->currentUser->get($r);
        $albums = $this->photos->albumsOf($me['id']);

        ob_start();
        require ROOT_PATH . '/templates/photos/albums.php';
        return new Response(ob_get_clean());
    }

    public function upload(Request $r): Response
    {
        $me = $this->currentUser->get($r);

        if (!isset($_FILES['photo'])) {
            return new Response('No file', 400);
        }

        $albumId = (int)$r->post('album_id');
        $isAvatar = (bool)$r->post('is_avatar');

        $dir = ROOT_PATH . '/public/uploads/photos/' . $me['id'];
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $name = uniqid() . '.jpg';
        $path = $dir . '/' . $name;

        move_uploaded_file($_FILES['photo']['tmp_name'], $path);

        $this->photos->addPhoto(
            $me['id'],
            $albumId,
            '/uploads/photos/' . $me['id'] . '/' . $name,
            $isAvatar
        );

        return (new Response('', 302))
            ->withHeader('Location', '/photos');
    }

    public function uploadAvatar(Request $r): Response
    {
        $user = $this->currentUser->get($r);

        if (
            empty($_FILES['avatar']) ||
            $_FILES['avatar']['error'] !== UPLOAD_ERR_OK
        ) {
            return new Response('', 302, ['Location' => '/profile/edit']);
        }

        $file = $_FILES['avatar'];

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return new Response('', 302, ['Location' => '/profile/edit']);
        }

        // 📁 директория
        $dir = ROOT_PATH . '/public/uploads/avatars';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // 📄 имя файла
        $name = 'avatar_' . $user['id'] . '.' . $ext;
        $fullPath = $dir . '/' . $name;
        $dbPath = '/uploads/avatars/' . $name;

        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            throw new \RuntimeException('Не удалось сохранить файл');
        }

        // 🧠 записываем в БД
        $this->photos->setAvatar($user['id'], $dbPath);

        return (new Response('', 302))
            ->withHeader('Location', '/profile');
    }

}
