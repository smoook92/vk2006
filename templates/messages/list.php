<?php
$title = 'Сообщения';
ob_start();
?>

<table width="100%" cellpadding="5">
<?php if (empty($dialogs)): ?>
<tr>
    <td style="color:#777;">Нет сообщений</td>
</tr>
<?php endif; ?>

<?php foreach ($dialogs as $d): ?>
    <?php
        $isOnline = false;

        if (!empty($d['last_seen_at'])) {
            $lastSeen = new DateTime($d['last_seen_at']);
            $now = new DateTime();
            $isOnline = ($now->getTimestamp() - $lastSeen->getTimestamp()) <= 300;
        }
    ?>

    <tr onclick="location.href='/messages/dialog?user_id=<?= (int)$d['id'] ?>'"
        style="cursor:pointer;
        <?= $d['has_unread'] ? 'background:#eef3f8; font-weight:bold;' : '' ?>">

        <td width="180">
            <b>
                <?= htmlspecialchars($d['first_name']) ?>
                <?= htmlspecialchars($d['last_name']) ?>
            </b>

            <?php if ($isOnline): ?>
                <span style="color:#4CAF50; font-size:10px;">● онлайн</span>
            <?php else: ?>
                <span style="color:#777; font-size:10px;">● оффлайн</span>
            <?php endif; ?>

            <br>
            <a href="/id<?= (int)$d['id'] ?>"
               onclick="event.stopPropagation();"
               style="font-size:10px; color:#777;">
                профиль
            </a>
        </td>

        <td>
            <?= htmlspecialchars(mb_strimwidth($d['body'], 0, 80, '…')) ?>
            <div style="font-size:10px; color:#777;">
                <?= \App\Service\DateTimeFormatter::format($d['created_at']) ?>
            </div>
        </td>

    </tr>
<?php endforeach; ?>
</table>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
