<?php
$request = $request ?? null;
$isAuth = $isAuth ?? ($request?->getAttribute('isAuth') ?? false);
$unreadMessages = $unreadMessages ?? ($request?->getAttribute('unreadMessages') ?? 0);
$friendRequests = $friendRequests ?? ($request?->getAttribute('friendRequests') ?? 0);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title ?? 'ВКонтакте') ?></title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f0f2f5;
            font-family: Tahoma, Verdana, Arial, sans-serif;
            font-size: 11px;
            color: #000;
        }

        a {
            color: #2b587a;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* === Основной контейнер === */
        .page {
            width: 720px;
            margin: 30px auto;
            background: #fff;
            border: 1px solid #d7d7d7;
        }

        /* === Шапка (НЕ на всю ширину!) === */
        .header {
            background: #4a76a8;
            color: #fff;
            padding: 6px 10px;
        }

        .header table {
            width: 100%;
        }

        .logo {
            font-weight: bold;
            font-size: 13px;
        }

        .nav {
            text-align: right;
            font-size: 11px;
        }

        .nav a {
            color: #fff;
            margin-left: 10px;
        }

        /* === Контент === */
        .content {
            padding: 15px;
        }

        /* === Футер === */
        .footer {
            padding: 8px 15px;
            border-top: 1px solid #e0e0e0;
            background: #f5f5f5;
            color: #777;
            font-size: 10px;
        }
    </style>
</head>
<body>

<div class="page">

    <!-- ШАПКА -->
    <div class="header">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td class="logo">
                    <a href="/profile" style="color:#fff;">ВКонтакте</a>
                </td>
                <?php if ($isAuth): ?>
                    <td class="nav">
                        <a href="/profile">Моя страница</a>
                        <a href="/friends">
                            Друзья
                            <?php if ($friendRequests > 0): ?>
                                <span style="color:#cfcfcf;">(<?= $friendRequests ?>)</span>
                            <?php endif; ?>
                        </a>
                        <a href="/messages" style="<?= $unreadMessages ? 'font-weight:bold;' : '' ?>">
                            Сообщения
                            <?php if (!empty($unreadMessages)): ?>
                                <span style="color:#ccc;">(<?= $unreadMessages ?>)</span>
                            <?php endif; ?>
                        </a>

                        <a href="/friends/requests" style="<?= $friendRequests ? 'font-weight:bold;' : '' ?>">
                            Заявки
                            <?php if (!empty($friendRequests)): ?>
                                <span style="color:#ccc;">(<?= $friendRequests ?>)</span>
                            <?php endif; ?>
                        </a>
                        <a href="/search">Поиск</a>
                        <a href="/logout">Выход</a>
                    </td>
                <?php endif; ?>
            </tr>
        </table>
    </div>

    <!-- КОНТЕНТ -->
    <div class="content">
        <?= $content ?>
    </div>

    <!-- ФУТЕР -->
    <div class="footer">
        © 2006 ВКонтакте
    </div>

</div>

</body>
</html>
