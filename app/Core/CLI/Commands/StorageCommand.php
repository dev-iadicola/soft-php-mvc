<?php

declare(strict_types=1);

namespace App\Core\CLI\Commands;

use App\Core\Mvc;
use App\Core\Config;
use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;

class StorageCommand implements CommandInterface
{
    public function exe(array $command): void
    {
        $action = $command[3] ?? null;
        if (!$action) {
            Out::warn('Usage: php soft storage --link|--clear|--backup|--create <disk>');
            return;
        }
        $this->registerCommands($command);
        Out::success("Storage command executed successfully.");
    }

    private function registerCommands($command)
    {
        $listOfCommands = [
            '--link' => 'Link the storage directory to the public directory',
            '--clear' => 'Clear the storage directory',
            '--backup' => 'Backup the storage directory',
            '--create' => $this->create($command[4]),
        ];
        $action = $command[3] ?? null;
        return match ($action) {
            '--link' => $this->linkStorage(),
            '--clear' => $listOfCommands['--clear'],
            '--backup' => $listOfCommands['--backup'],
            '--create' => $this->create($command[4] ?? ''),
            default => Out::warn("Unknown action: {$action}"),
        };
    }

    private function create($disk){
        if ($disk === '') {
            Out::warn('Missing disk name. Example: php soft storage --create public');
            return;
        }
        mkdir(getcwd() . "/storage/$disk", 0775, true);
    }

    private function linkStorage(): void
    {
        $root = getcwd();
        $publicDir = $root . '/public';

        if (is_dir($publicDir)) {
            $target = $root . '/storage/app/public';
            $link = $publicDir . '/storage';

            if (is_link($link) || file_exists($link)) {
                Out::warn("Link already exists: {$link}");
                return;
            }

            if (!is_dir($target)) {
                mkdir($target, 0775, true);
            }

            symlink($target, $link);
            Out::info("Linked: {$link} -> {$target}");
            return;
        }

        // No public/ directory: fallback to /storage/images mapping
        $target = $root . '/storage/app/public/images';
        $link = $root . '/storage/images';

        if (is_link($link) || file_exists($link)) {
            Out::warn("Link already exists: {$link}");
            return;
        }

        if (!is_dir($target)) {
            mkdir($target, 0775, true);
        }

        symlink($target, $link);
        Out::info("Linked: {$link} -> {$target}");
    }

    
    private function mvc(): Mvc
    {
        echo getcwd() . '/.env';
        Config::env(getcwd() . '/.env');
        $config = Config::dir(getcwd() . '/config');
        setMvc($config);
        return mvc();
    }
}
