<?php

namespace App\Enums;

enum ForkListing: string
{
    case Automatic = 'automatic';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Automatic => __('messages.fork_listing_automatic'),
            self::Manual => __('messages.fork_listing_manual'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Automatic => __('messages.fork_listing_automatic_desc'),
            self::Manual => __('messages.fork_listing_manual_desc'),
        };
    }
}
