<?php

namespace App\Enums;

enum Position: string
{
    case LEFT = 'Left';
    case RIGHT = 'Right';

    public function label(): string
    {
        return match ($this) {
            self::LEFT => 'Left',
            self::RIGHT => 'Right',
        };
    }
}
