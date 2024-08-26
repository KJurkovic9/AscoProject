<?php

namespace App\Form;

abstract class WorldOrientation
{
    public const ZAPAD = "Z";
    public const SJEVEROZAPAD = "SZ";
    public const JUGOZAPAD = "JZ";
    public const JUG = "J";
    public const JUGOISTOK = "JI";
    public const ISTOK = "I";
    public const SJEVEROISTOK = "SJ";
    public const SJEVER = "S";

    public static function toArray(): array
    {
        return [
            self::ZAPAD => 'Z',
            self::SJEVEROZAPAD => 'SZ',
            self::JUGOZAPAD => 'JZ',
            self::JUG => 'J',
            self::JUGOISTOK => 'JI',
            self::ISTOK => 'I',
            self::SJEVEROISTOK => 'SI',
            self::SJEVER => 'S',
        ];
    }
}
