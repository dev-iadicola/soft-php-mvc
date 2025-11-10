<?php

namespace App\Core\CLI\Commands;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;

class ServeCommand implements CommandInterface
{

    private string $host = '127.0.0.1';
    private string $port = '8000';
    private string $root = '/';
    private bool $open = false;
    private bool $https = false;
    private bool $quiet = false;
    private string $phpBin = 'php';
    public function exe(array $command): void
    {
           $this->root = getcwd();
        $this->parseOptions($command);
     

        if (!is_dir($this->root)) {
            Out::error(" index.php  not found. Please check your project");
        }

        if (!file_exists($this->root . '/index.php')) {
            Out::error("Missing 'index.php'.");
        }

        if ($this->isPortInUse($this->port)) {
            Out::warn("⚠️  Port $this->port is already in use. Try another port.");
            exit(1);
        }

        if (!$this->quiet) {
            Out::ok("🚀 Starting development server...");
            Out::ln("🌐 Listening on: " . ($this->https ? "https" : "http") . "://{$this->host}:{$this->port}");
            Out::ln("📁 Serving directory: $this->root");
            Out::ln("🛑 Press Ctrl+C to stop the server\n");
        }

        // Se l'utente ha passato l'opzione --https, usa il protocollo TLS, altrimenti HTTP normale.
        // ? (Nota: PHP -S non supporta nativamente HTTPS, ma questa variabile può servire per log o estensioni future)
        $protocol = $this->https ? "tls://" : "";

        // Costruisce il comando da eseguire nel terminale per avviare il server di sviluppo PHP.
        // - $this->phpBin: path all'eseguibile PHP (es. "php" o "/usr/bin/php8.3")
        // - -S: flag che avvia il server interno di PHP
        // - {$this->host}:{$this->port}: indirizzo e porta su cui il server ascolterà
        // - -t {$this->root}: cartella root del progetto (es. "public")
        $cmd = "{$this->phpBin} -S {$this->host}:{$this->port} -t {$this->root}";

        // Esegue il comando costruito sopra mantenendo l'output visibile nel terminale in tempo reale.
        // A differenza di exec() o shell_exec(), passthru() non cattura l'output ma lo mostra direttamente.
        // Il processo resta attivo finché non viene interrotto (Ctrl + C).
        passthru($cmd);

        // Se l'opzione --open è abilitata, apre automaticamente il browser di default
        // con l'indirizzo del server, adattandosi al sistema operativo corrente:
        $url ="http://{$this->host}:{$this->port}";
        if ($this->open && PHP_OS_FAMILY === 'Windows') {
            // Su Windows, il comando "start" apre l'URL nel browser predefinito.
            // exec("explorer \"$url\"");
            Out::info("Open not work in widnows");
        } elseif ($this->open && PHP_OS_FAMILY === 'Darwin') {
            // Su macOS, "open" apre l'URL nel browser di sistema.
            // TODO: Da testare con VM
            exec("open $url");
        } elseif ($this->open) {
            // Su Linux, "xdg-open" apre l'URL nel browser predefinito del desktop environment.
            // TODO: da testare con vm
            exec("xdg-open $url");
        }



        passthru("php -S $this->host:$this->port");
    }

    /**
     * Analize params CLI
     */
    private function parseOptions(array $args): void
    {
        foreach ($args as $i => $arg) {
            switch ($arg) {
                case '--port':
                case '-p':
                    $this->port = $args[$i + 1] ?? $this->port;
                    break;

                case '--host':
                case '-h':
                    $this->host = $args[$i + 1] ?? $this->host;
                    break;

                case '--root':
                    $this->root = $args[$i + 1] ?? $this->root;
                    break;

                case '--open':
                case '-o':
                    $this->open = true;
                    break;

                case '--https':
                    $this->https = true;
                    break;

                case '--quiet':
                case '-q':
                    $this->quiet = true;
                    break;

                case '--php':
                    $this->phpBin = $args[$i + 1] ?? $this->phpBin;
                    break;
            }
        }
    }



    private function isPortInUse($port): bool
    {
        $connection =  @fsockopen('localhost', $port);
        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }

        return false;
    }
}
