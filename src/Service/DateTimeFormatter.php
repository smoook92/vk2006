<?php
declare(strict_types=1);

namespace App\Service;

final class DateTimeFormatter
{
    public static function format(string $datetime): string
    {
        $dt  = new \DateTime($datetime);
        $now = new \DateTime();

        // сегодня
        if ($dt->format('Y-m-d') === $now->format('Y-m-d')) {
            return 'сегодня в ' . $dt->format('H:i');
        }

        // вчера
        $yesterday = (clone $now)->modify('-1 day');
        if ($dt->format('Y-m-d') === $yesterday->format('Y-m-d')) {
            return 'вчера в ' . $dt->format('H:i');
        }

        // иначе дата
        return $dt->format('d.m.Y H:i');
    }
}
