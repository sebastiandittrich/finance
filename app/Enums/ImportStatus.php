<?php

namespace App\Enums;

enum ImportStatus
{
    case Running;
    case Done;
    case Failed;

    public function color()
    {
        return match ($this) {
            self::Running => '#ff0000',
            self::Done => '#00ff00',
            self::Failed => '#0000ff',
        };
    }
}
