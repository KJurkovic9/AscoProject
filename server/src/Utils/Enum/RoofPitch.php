<?php

namespace App\Form;

abstract class RoofPitch
{
    public const ZERODEG = 0;
    public const FIFTEENDEG = 15;
    public const THIRTYDEG = 30;
    public const THIRTYFOURDEG = 34;
    public const FOURTHYFIVEDEG = 45;
    public const SIXTHYDEG = 60;
    public const NINETYDEG = 90;

    public static function toArray(): array
    {
        return [
            self::ZERODEG => 0,
            self::FIFTEENDEG => 15,
            self::THIRTYDEG => 30,
            self::THIRTYFOURDEG => 34,
            self::FOURTHYFIVEDEG => 45,
            self::SIXTHYDEG => 60,
            self::NINETYDEG => 90,
        ];
    }
}
