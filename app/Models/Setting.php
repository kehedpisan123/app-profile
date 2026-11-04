<?php

namespace App\Models;

use App\Enums\SettingType;
use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['group', 'key', 'label', 'value', 'type'])]
class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => SettingType::class,
        ];
    }

    /**
     * Resolve the stored string value into the shape expected by API consumers.
     */
    public function resolvedValue(): mixed
    {
        if ($this->value === null || $this->value === '') {
            return $this->type === SettingType::Boolean ? false : null;
        }

        return match ($this->type) {
            SettingType::Boolean => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            SettingType::Json => json_decode($this->value, true),
            SettingType::Image => Storage::disk('public')->url($this->value),
            default => $this->value,
        };
    }
}
