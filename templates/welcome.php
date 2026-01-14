<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>ВКонтакте</title>

    <style>
        /* === Base === */
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

        /* === Page layout === */
        .wrap {
            width: 720px;
            margin: 40px auto;
            background: #ffffff;
            border: 1px solid #d7d7d7;
        }

        /* === Header === */
        .header {
            background: #4a76a8;
            color: #ffffff;
            padding: 10px 15px;
            font-size: 14px;
            font-weight: bold;
        }

        /* === Content === */
        .content {
            padding: 20px 25px;
        }

        .content p {
            margin: 8px 0;
            line-height: 1.4;
        }

        /* === Actions === */
        .actions {
            margin-top: 20px;
        }

        .actions a {
            margin-right: 20px;
            font-weight: bold;
        }

        /* === Footer === */
        .footer {
            padding: 10px 25px;
            background: #f5f5f5;
            border-top: 1px solid #e0e0e0;
            color: #777;
            font-size: 10px;
        }
    </style>
</head>
<body>

<div class="wrap">

    <div class="header">
        ВКонтакте
    </div>

    <div class="content">
        <p>
            ВКонтакте — это закрытая социальная сеть для общения
            с друзьями, однокурсниками и знакомыми.
        </p>

        <p>
            Регистрация возможна только по персональному приглашению.
        </p>

        <div class="actions">
            <a href="/login">Войти</a>
            <a href="/register">Регистрация</a>
        </div>
    </div>

    <div class="footer">
        © 2006 ВКонтакте
    </div>

</div>

</body>
</html>
