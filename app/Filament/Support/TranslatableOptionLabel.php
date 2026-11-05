<?php

namespace App\Filament\Support;

use Illuminate\Database\Eloquent\Model;

class TranslatableOptionLabel
{
    /**
     * Label a related record with the translation for the active application locale.
     */
    public static function for(string $attribute): \Closure
    {
        return function (Model $record) use ($attribute): string {
            if (method_exists($record, 'getTranslation')) {
                return (string) $record->getTranslation($attribute, app()->getLocale(), true);
            }

            return (string) $record->getAttribute($attribute);
        };
    }
}
