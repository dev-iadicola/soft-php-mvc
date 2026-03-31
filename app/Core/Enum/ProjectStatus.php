<?php

declare(strict_types=1);

namespace App\Core\Enum;

enum ProjectStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Paused = 'paused';

    public function label(): string
    {
        return match ($this) {
            self::InProgress => 'In corso',
            self::Completed => 'Completato',
            self::Paused => 'In pausa',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::InProgress => 'var(--accent-orange)',
            self::Completed => 'var(--accent-green)',
            self::Paused => 'var(--text-muted)',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::InProgress => 'fa-spinner',
            self::Completed => 'fa-check-circle',
            self::Paused => 'fa-pause-circle',
        };
    }
}
