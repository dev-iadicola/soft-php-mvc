<?php
namespace App\Core\CLI\Commands\Clear;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;

class ClearCacheCommand implements CommandInterface
{
    public function exe(array $args)
    {
        Out::info("🧹 Avvio pulizia cache PHP...");

        // OPcache reset
        if (function_exists('opcache_reset')) {
            if (@opcache_reset()) {
                Out::success("✅ OPcache svuotata.");
            } else {
                Out::warning("⚠️  OPcache attiva ma non svuotabile (forse disabilitata in CLI?).");
            }
        } else {
            Out::info("ℹ️  OPcache non disponibile.");
        }

        // Realpath cache
        clearstatcache();
        Out::success("✅ Realpath cache svuotata.");

        // APCu
        if (function_exists('apcu_clear_cache')) {
            apcu_clear_cache();
            Out::success("✅ APCu cache svuotata.");
        }

        Out::success("🎉 Pulizia completata.");

    }
}