<?php
$title = 'Поиск людей';
ob_start();
?>

<h2>Поиск людей</h2>

<form method="post" action="/search">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">
    <table cellpadding="4">

    <tr>
        <td>Имя или фамилия:</td>
        <td><input type="text" name="name"></td>
    </tr>

    <tr>
        <td>Город:</td>
        <td><input type="text" name="city"></td>
    </tr>

    <tr>
        <td>ВУЗ:</td>
        <td><input type="text" name="university"></td>
    </tr>

    <tr>
        <td>Факультет:</td>
        <td><input type="text" name="faculty"></td>
    </tr>

    <tr>
        <td>Год поступления:</td>
        <td><input type="text" name="enrollment_year"></td>
    </tr>

    <tr>
        <td></td>
        <td><button type="submit">Найти</button></td>
    </tr>

    </table>
</form>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
