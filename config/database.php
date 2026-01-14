<?php
declare(strict_types=1);

return new PDO(
    'pgsql:host=localhost;port=5432;dbname=social',
    'postgres',
    'postgres', // или пусто
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

