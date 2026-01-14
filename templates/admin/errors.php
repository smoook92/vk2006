<?php
$title = 'Ошибки';
ob_start();
?>

<h2>Лог ошибок</h2>

<pre style="font-size:11px; background:#f5f5f5; padding:10px; border:1px solid #ccc; max-height:400px; overflow:auto;">
<?= htmlspecialchars($content) ?>
</pre>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
