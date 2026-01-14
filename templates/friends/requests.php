<?php
$title = 'Заявки в друзья';
ob_start();
?>

<h3>Заявки в друзья</h3>

<?php if (empty($requests)): ?>
    <p style="color:#777;">Новых заявок нет</p>
<?php else: ?>

<table cellpadding="5" cellspacing="0">
<?php foreach ($requests as $r): ?>
<tr>
    <td>
        <a href="/id<?= (int)$r['user_id'] ?>">
            <?= htmlspecialchars($r['first_name']) ?>
            <?= htmlspecialchars($r['last_name']) ?>
        </a>
    </td>
    <td>
        <form method="post" action="/friends/accept" style="margin:0;">
            <input type="hidden" name="request_id" value="<?= (int)$r['id'] ?>">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">
            <button>Принять</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php endif; ?>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
