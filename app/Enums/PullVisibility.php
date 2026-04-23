<?php

namespace App\Enums;

enum PullVisibility: string
{
    case Everyone = 'everyone';
    case MembersOnly = 'members_only';
    case AdminsOnly = 'admins_only';

    public function label(): string
    {
        return match ($this) {
            self::Everyone => __('messages.pull_vis_everyone'),
            self::MembersOnly => __('messages.pull_vis_members_only'),
            self::AdminsOnly => __('messages.pull_vis_admins_only'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Everyone => __('messages.pull_vis_everyone_desc'),
            self::MembersOnly => __('messages.pull_vis_members_only_desc'),
            self::AdminsOnly => __('messages.pull_vis_admins_only_desc'),
        };
    }
}
