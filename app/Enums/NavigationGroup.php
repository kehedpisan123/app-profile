<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup implements HasLabel
{
    case Content;
    case PublicQuestions;
    case System;

    public function getLabel(): string
    {
        return match ($this) {
            self::Content => __('Content'),
            self::PublicQuestions => __('Public Questions'),
            self::System => __('System'),
        };
    }
}
