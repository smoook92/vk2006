<?php
$title = 'Мои инвайты';
ob_start();
?>

<h2>Приглашения</h2>

<form method="post" action="/invites/create">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">
    <button>Создать приглашение</button>
</form>

<br>

<table cellpadding="4">
<tr>
    <th>Токен</th>
    <th>Статус</th>
    <th>Истекает</th>
</tr>

<?php foreach ($list as $i): ?>
<tr>
    <td><?= htmlspecialchars($i['token']) ?></td>
    <td>
        <?php if ($i['used_at']): ?>
            Использован
        <?php elseif (strtotime($i['expires_at']) < time()): ?>
            Истёк
        <?php else: ?>
            Активен
        <?php endif; ?>
    </td>
    <td>
        <?= date('d.m.Y', strtotime($i['expires_at'])) ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
