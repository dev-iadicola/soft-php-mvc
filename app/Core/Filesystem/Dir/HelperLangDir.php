<?php

declare(strict_types=1);

namespace App\Core\Filesystem\Dir;

/**
 * Directory object for helper language resource files.
 */
final class HelperLangDir extends Dir
{
    /**
     * Create a directory object that points to App/Core/Helpers/Lang.
     */
    public static function instance(): self
    {
        return new self(CoreDir::instance()->path('Helpers/Lang'));
    }
}
