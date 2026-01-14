<?php
$title = 'Регистрация';

ob_start();
?>

<h2>Регистрация</h2>

<form method="post" action="/register">
<table>
<tr>
    <td>Инвайт:</td>
    <td><input type="text" name="invite"></td>
</tr>
<tr>
    <td>Имя:</td>
    <td><input type="text" name="first_name"></td>
</tr>
<tr>
    <td>Фамилия:</td>
    <td><input type="text" name="last_name"></td>
</tr>
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
    <td><button type="submit">Зарегистрироваться</button></td>
</tr>
</table>
</form>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
