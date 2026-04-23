<?php

namespace App\Enums;

enum ProjectVisibility: string
{
    case Private = 'private';
    case Public = 'public';
    case Restricted = 'restricted';

    public function label(): string
    {
        return match ($this) {
            self::Private => __('messages.visibility_private'),
            self::Public => __('messages.visibility_public'),
            self::Restricted => __('messages.visibility_restricted'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Private => __('messages.visibility_private_desc'),
            self::Public => __('messages.visibility_public_desc'),
            self::Restricted => __('messages.visibility_restricted_desc'),
        };
    }
}
