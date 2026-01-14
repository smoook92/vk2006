<?php
$title = 'Редактирование страницы';

ob_start();
?>

<h2>Редактирование страницы</h2>

<form method="post" action="/profile/edit">
    <input type="hidden" name="_csrf"
           value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">
<table cellpadding="4">

<tr>
    <td>Имя:</td>
    <td><input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>"></td>
</tr>

<tr>
    <td>Фамилия:</td>
    <td><input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>"></td>
</tr>

<tr>
    <td>Дата рождения:</td>
    <td><input type="date" name="birth_date" value="<?= htmlspecialchars($user['birth_date'] ?? '') ?>"></td>
</tr>

<tr>
    <td>Город:</td>
    <td><input type="text" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>"></td>
</tr>

<tr>
    <td>ВУЗ:</td>
    <td><input type="text" name="university" value="<?= htmlspecialchars($user['university'] ?? '') ?>"></td>
</tr>

<tr>
    <td>Факультет:</td>
    <td><input type="text" name="faculty" value="<?= htmlspecialchars($user['faculty'] ?? '') ?>"></td>
</tr>

<tr>
    <td>Год поступления:</td>
    <td><input type="text" name="enrollment_year" value="<?= htmlspecialchars($user['enrollment_year'] ?? '') ?>"></td>
</tr>

<tr>
    <td>О себе:</td>
    <td><textarea name="about" rows="3" cols="30"><?= htmlspecialchars($user['about'] ?? '') ?></textarea></td>
</tr>

<tr>
    <td>Интересы:</td>
    <td><textarea name="interests" rows="3" cols="30"><?= htmlspecialchars($user['interests'] ?? '') ?></textarea></td>
</tr>
</table>
<button type="submit">Сохранить</button>
<a href="/profile">Отмена</a>
</form>
<hr>
<h3>Аватар</h3>

<form method="post"
      action="/profile/avatar"
      enctype="multipart/form-data">

    <input type="file" name="avatar" accept="image/*" required>

    <input type="hidden" name="_csrf"
           value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">

    <br><br>
    <button type="submit">Загрузить аватар</button>
</form>


<hr>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
