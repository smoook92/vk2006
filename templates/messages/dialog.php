<?php
$title = 'Диалог с ' . htmlspecialchars($other['first_name']);
ob_start();
?>

<h3 style="margin-bottom:4px;">
    Диалог с
    <a href="/id<?= (int)$other['id'] ?>">
        <?= htmlspecialchars($other['first_name']) ?>
        <?= htmlspecialchars($other['last_name']) ?>
    </a>
</h3>

<div style="font-size:11px; color:#777; margin-bottom:10px;">
    <?php if ($isOnline): ?>
        <span style="color:#4CAF50;">● онлайн</span>
    <?php else: ?>
        <?= htmlspecialchars($lastSeenText) ?>
    <?php endif; ?>
</div>

<table width="100%" cellpadding="4">
<?php if (empty($messages)): ?>
    <tr>
        <td style="color:#777;">
            Сообщений пока нет
        </td>
    </tr>
<?php else: ?>
    <?php foreach ($messages as $m): ?>
        <?php
            $isUnread = isset($m['is_read'], $m['receiver_id'], $me['id'])
                && !$m['is_read']
                && $m['receiver_id'] === $me['id'];
        ?>
        <tr style="<?= $isUnread ? 'background:#eef3f8;' : '' ?>">
            <td width="120" valign="top">
                <b>
                    <?= $m['sender_id'] == $me['id'] ? 'Вы' : htmlspecialchars($other['first_name']) ?>
                </b><br>
                <span style="color:#777; font-size:10px;">
                    <?= \App\Service\DateTimeFormatter::format($m['created_at']) ?>
                </span>
                
                <?php if ($m['sender_id'] === $me['id']): ?>
                    <span style="color:#777; font-size:10px; margin-left:4px;">
                        <?= $m['is_read'] ? '✓✓' : '✓' ?>
                    </span>
                <?php endif; ?>
            </td>
            <td>
                <?= nl2br(htmlspecialchars($m['body'])) ?>

                <?php if ($m['sender_id'] === $me['id'] && $m['is_read']): ?>
                    <div style="font-size:10px; color:#777; margin-top:4px;">
                        прочитано
                    </div>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
</table>

<hr>

<form method="post" action="/messages/send">
    <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['_csrf']) ?>">
    <input type="hidden" name="user_id" value="<?= (int)$other['id'] ?>">

    <textarea name="body" rows="4" cols="60" required></textarea><br><br>
    <button type="submit">Отправить</button>
</form>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/layout.php';
