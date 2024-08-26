<?php

namespace App\Controller;

abstract class OfferState
{
    public const SENT = "SENT";
    public const REJECTED = "REJECTED";
    public const DONE = "DONE";
    public const ACCEPTED = "ACCEPTED";
    public const CHOSEN = "CHOSEN";
    // this is when user declines the offer
    public const DECLINED = "DECLINED";

    public static function toArray(): array
    {
        return [
            self::SENT => "SENT",
            self::REJECTED => "REJECTED",
            self::DONE => "DONE",
            self::ACCEPTED => "ACCEPTED",
            self::CHOSEN => "CHOSEN",
            self::DECLINED => "DECLINED"
        ];
    }
}
