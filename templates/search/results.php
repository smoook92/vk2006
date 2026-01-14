<?php
$title = 'Результаты поиска';
ob_start();
?>

<h2>Результаты поиска</h2>

<?php if (empty($results)): ?>
    <p>Ничего не найдено.</p>
<?php else: ?>

<table cellpadding="4" cellspacing="0" width="100%">
<?php foreach ($results as $u): ?>
<tr>
    <td width="200">
        <a href="/id<?= (int)$u['id'] ?>">
            <?= htmlspecialchars($u['first_name']) ?>
            <?= htmlspecialchars($u['last_name']) ?>
        </a>

    </td>
    <td width="150">
        <?= htmlspecialchars($u['city'] ?? '') ?>
    </td>
    <td>
        <?= htmlspecialchars($u['university'] ?? '') ?>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php endif; ?>

<p style="margin-top:15px;">
    <a href="/search">← Новый поиск</a>
</p>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
