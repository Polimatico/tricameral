<?php

namespace App\Enums;

enum ProjectRole: string
{
    case Viewer = 'viewer';
    case Editor = 'editor';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Viewer => __('messages.role_viewer'),
            self::Editor => __('messages.role_editor'),
            self::Admin => __('messages.role_admin'),
        };
    }
}
