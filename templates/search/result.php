<?php
$title = 'Результаты поиска';
ob_start();
?>

<h2>Результаты поиска</h2>

<?php if (!$results): ?>
    <p>Ничего не найдено.</p>
<?php else: ?>
<table cellpadding="4">
<?php foreach ($results as $u): ?>
<tr>
    <td>
        <a href="/id<?= $u['id'] ?>">
            <?= htmlspecialchars($u['first_name']) ?>
            <?= htmlspecialchars($u['last_name']) ?>
        </a>
    </td>
    <td>
        <?= htmlspecialchars($u['city'] ?? '') ?>
    </td>
    <td>
        <?= htmlspecialchars($u['university'] ?? '') ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<p><a href="/search">Новый поиск</a></p>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
