<?php

namespace App\Enums\Learning;

enum ModuleLevel: string
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'advanced';
    case Quant = 'quant';

    public function label(): string
    {
        return match ($this) {
            self::Beginner => 'Pemula',
            self::Intermediate => 'Menengah',
            self::Advanced => 'Lanjutan',
            self::Quant => 'Kuantitatif',
        };
    }
}
