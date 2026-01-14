<?php
$title = 'Моя страница';
ob_start();
$avatar = $this->photos->avatarOf($user['id']);

?>

<table width="100%" cellspacing="0" cellpadding="5">
<tr>
    <!-- Левая колонка -->
    <td width="200" valign="top">
        <div style="border:1px solid #d7d7d7; padding:5px; text-align:center;">
           <?php if ($avatar): ?>
                <img src="<?= htmlspecialchars($avatar['path']) ?>" width="180">
            <?php else: ?>
                <img src="/images/avatar_placeholder.gif" width="180">
            <?php endif; ?>


        </div>

        <p style="text-align:center; margin-top:10px;">
            <a href="/profile/edit">Редактировать страницу</a>
        </p>
    </td>

    <!-- Правая колонка -->
    <td valign="top">
        <h2 style="margin-top:0;">
            <?= htmlspecialchars($user['first_name']) ?>
            <?= htmlspecialchars($user['last_name']) ?>
        </h2>

        <table cellpadding="3">
            <tr>
                <td><b>Email:</b></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
            </tr>

            <?php if (!empty($user['birth_date'])): ?>
            <tr>
                <td><b>Дата рождения:</b></td>
                <td><?= htmlspecialchars($user['birth_date']) ?></td>
            </tr>
            <?php endif; ?>

            <?php if (!empty($user['city'])): ?>
            <tr>
                <td><b>Город:</b></td>
                <td><?= htmlspecialchars($user['city']) ?></td>
            </tr>
            <?php endif; ?>

            <?php if (!empty($user['university'])): ?>
            <tr>
                <td><b>ВУЗ:</b></td>
                <td><?= htmlspecialchars($user['university']) ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </td>
</tr>
</table>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
