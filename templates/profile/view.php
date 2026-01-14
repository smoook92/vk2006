<?php
$title = htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']);
ob_start();
?>

<table width="100%" cellpadding="5">
<tr>

    <!-- ЛЕВАЯ КОЛОНКА -->
    <td width="200" valign="top">

        <!-- Аватар -->
        <div style="border:1px solid #d7d7d7; padding:5px; text-align:center;">
            <?php if (!empty($avatar)): ?>
                <img src="<?= htmlspecialchars($avatar['path']) ?>" width="180" alt="avatar">
            <?php else: ?>
                <img src="/images/avatar_placeholder.gif" width="180" alt="no avatar">
            <?php endif; ?>
        </div>

    </td>

    <!-- ПРАВАЯ КОЛОНКА -->
    <td valign="top">

        <!-- Имя -->
        <h2 style="margin-top:0;">
            <?= htmlspecialchars($profile['first_name']) ?>
            <?= htmlspecialchars($profile['last_name']) ?>
        </h2>

        <!-- Онлайн статус -->
        <div style="font-size:11px; margin-bottom:8px; color:<?= $isOnline ? '#4CAF50' : '#777' ?>;">
            <?= htmlspecialchars($lastSeenText) ?>
        </div>

        <!-- Неавторизован -->
        <?php if (!$isAuth): ?>
            <p style="color:#777;">
                <a href="/login">Войдите</a>, чтобы добавить в друзья или написать сообщение
            </p>
        <?php endif; ?>

        <!-- ДЕЙСТВИЯ -->
        <?php if ($isAuth && !$isOwner): ?>

            <!-- Написать сообщение -->
            <?php if ($status === 'friends'): ?>
                <p>
                    <a href="/messages/dialog?user_id=<?= (int)$profile['id'] ?>">
                        ✉ Написать сообщение
                    </a>
                </p>
            <?php endif; ?>

            <!-- Добавить в друзья -->
            <?php if ($status === 'none'): ?>
                <form method="post" action="/friends/add">
                    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">
                    <input type="hidden" name="user_id" value="<?= (int)$profile['id'] ?>">
                    <button>Добавить в друзья</button>
                </form>
            <?php endif; ?>

            <!-- Заявка отправлена -->
            <?php if ($status === 'outgoing'): ?>
                <p><i>Заявка отправлена</i></p>
            <?php endif; ?>

            <!-- Входящая заявка -->
            <?php if ($status === 'incoming'): ?>
                <p><i>Отправил вам заявку</i></p>
            <?php endif; ?>

        <?php endif; ?>

        <!-- ИНФОРМАЦИЯ -->
        <table cellpadding="3" style="margin-top:10px;">
            <?php if (!empty($profile['city'])): ?>
                <tr>
                    <td><b>Город:</b></td>
                    <td><?= htmlspecialchars($profile['city']) ?></td>
                </tr>
            <?php endif; ?>

            <?php if (!empty($profile['university'])): ?>
                <tr>
                    <td><b>ВУЗ:</b></td>
                    <td><?= htmlspecialchars($profile['university']) ?></td>
                </tr>
            <?php endif; ?>
        </table>

    </td>
</tr>
</table>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
