<?php
$title = 'Диалог';
ob_start();
?>

<table cellpadding="5">
<tr>
    <td width="60">
        <?php if (!empty($avatar)): ?>
            <img src="<?= htmlspecialchars($avatar['path']) ?>" width="50">
        <?php else: ?>
            <img src="/images/avatar_placeholder.gif" width="50">
        <?php endif; ?>
    </td>
    <td>
        <h2 style="margin:0;">
            <?= htmlspecialchars($other['first_name']) ?>
            <?= htmlspecialchars($other['last_name']) ?>
        </h2>
    </td>
</tr>
</table>

<div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
<?php foreach ($messages as $m): ?>
    <p>
        <b>
            <?= $m['from_user_id'] === $me['id'] ? 'Я' : htmlspecialchars($other['first_name']) ?>:
        </b>
        <?= nl2br(htmlspecialchars($m['body'])) ?>
    </p>
<?php endforeach; ?>
</div>

<form method="post" action="/messages/send">
    <input type="hidden" name="user_id" value="<?= (int)$other['id'] ?>">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">
    <textarea name="body" rows="3" cols="40"></textarea><br>
    <button>Отправить</button>
</form>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
