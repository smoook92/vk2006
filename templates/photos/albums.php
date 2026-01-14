<?php
$title = 'Фотографии';
ob_start();
?>

<h2>Фотографии</h2>

<form method="post" action="/photos/upload" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">
    <input type="hidden" name="album_id" value="<?= $albums[0]['id'] ?? 1 ?>">

    <p>
        <input type="file" name="photo">
    </p>

    <p>
        <label>
            <input type="checkbox" name="is_avatar" value="1">
            Сделать аватаром
        </label>
    </p>

    <button>Загрузить</button>
</form>

<hr>

<?php foreach ($albums as $a): ?>
    <h3><?= htmlspecialchars($a['title']) ?></h3>

    <?php
    $photos = $this->photos->photosInAlbum($a['id']);
    foreach ($photos as $p):
    ?>
        <img src="<?= htmlspecialchars($p['path']) ?>" width="120">
    <?php endforeach; ?>
<?php endforeach; ?>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
