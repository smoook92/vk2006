<?php
$title = 'Вход';

ob_start();
?>

<h2>Вход</h2>

<form method="post" action="/login">
<table>
<tr>
    <td>Email:</td>
    <td><input type="text" name="email"></td>
</tr>
<tr>
    <td>Пароль:</td>
    <td><input type="password" name="password"></td>
</tr>
<tr>
    <td></td>
    <td><button type="submit">Войти</button></td>
</tr>
</table>
<?php if (!empty($error)): ?>
    <p style="color:red;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>
</form>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
