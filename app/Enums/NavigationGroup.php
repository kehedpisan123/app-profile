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
            self::Content => 'Konten',
            self::PublicQuestions => 'Pertanyaan Masyarakat',
            self::System => 'Sistem',
        };
    }
}
