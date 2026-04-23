<?php

namespace App\Enums;

enum PullPermission: string
{
    case Everyone = 'everyone';
    case MembersOnly = 'members_only';
    case EditorsOnly = 'editors_only';
    case AdminsOnly = 'admins_only';
    case Disabled = 'disabled';

    public function label(): string
    {
        return match ($this) {
            self::Everyone => __('messages.pull_everyone'),
            self::MembersOnly => __('messages.pull_members_only'),
            self::EditorsOnly => __('messages.pull_editors_only'),
            self::AdminsOnly => __('messages.pull_admins_only'),
            self::Disabled => __('messages.pull_disabled'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Everyone => __('messages.pull_everyone_desc'),
            self::MembersOnly => __('messages.pull_members_only_desc'),
            self::EditorsOnly => __('messages.pull_editors_only_desc'),
            self::AdminsOnly => __('messages.pull_admins_only_desc'),
            self::Disabled => __('messages.pull_disabled_desc'),
        };
    }
}
