<?php
$title = 'Сообщения';

ob_start();
?>

<h2>Мои сообщения</h2>

<table cellpadding="5">
<?php foreach ($dialogs as $d): ?>
<tr>
    <td>
        <a href="/messages/dialog?user=<?= $d['user_id'] ?>">
            <?= htmlspecialchars($d['first_name']) ?>
            <?= htmlspecialchars($d['last_name']) ?>
        </a>
    </td>
    <td>
        <?= htmlspecialchars($d['last_message'] ?? '') ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
