<?php
$title = 'Друзья';
ob_start();
?>

<h2>Мои друзья</h2>

<?php if (empty($friends)): ?>
    <p>У вас пока нет друзей.</p>
<?php else: ?>

<table cellpadding="5" cellspacing="0">
<?php foreach ($friends as $f): ?>
<tr>
    <td width="50">
        <?php if (!empty($f['avatar'])): ?>
            <img
                src="<?= htmlspecialchars($f['avatar']['path']) ?>"
                width="40"
                height="40"
                alt=""
            >
        <?php else: ?>
            <img
                src="/images/avatar_placeholder.gif"
                width="40"
                height="40"
                alt=""
            >
        <?php endif; ?>
    </td>

    <td>
        <a href="/id<?= (int)$f['id'] ?>">
            <?= htmlspecialchars($f['first_name']) ?>
            <?= htmlspecialchars($f['last_name']) ?>
        </a>
        <?php if ($friend['is_online']): ?>
            <span style="color:#4CAF50; font-size:10px;">● онлайн</span>
        <?php else: ?>
            <span style="color:#777; font-size:10px;">● оффлайн</span>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php endif; ?>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
