<?php

namespace App\Enums;

enum TextDirection: string
{
    case LTR = 'ltr';
    case RTL = 'rtl';

    public function label(): string
    {
        return match ($this) {
            self::LTR => 'Left to Right',
            self::RTL => 'Right to Left',
        };
    }
}
