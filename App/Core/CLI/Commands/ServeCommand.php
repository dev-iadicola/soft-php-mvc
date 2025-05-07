<?php 
namespace App\Core\CLI\Commands;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;

class ServeCommand implements CommandInterface {
    public function exe(array $command): void
    {
        $host = $command[2] ?? '127.0.0.1';
        $port = $command[3] ?? '8000';
        $dir = getcwd();

        if (!is_dir($dir)) {
            Out::error(" index.php  not found. Please check your project");
        }

        if (!file_exists($dir . '/index.php')) {
            Out::error("Missing 'index.php' in the public directory.");
        }

        if ($this->isPortInUse($port)) {
            Out::warn("⚠️  Port $port is already in use. Try another port.");
            exit(1);
        }

        Out::ok("Starting development server...");
        Out::ln("🌐 Listening on: http://$host:$port");
        Out::ln("📁 Serving directory: $dir");
        Out::ln("🛑 Press Ctrl+C to stop the server\n");

        passthru("php -S $host:$port -t $dir");
    }

    private function isPortInUse($port): bool
    {
        $output = [];
        $cmd = (strncasecmp(PHP_OS, 'WIN', 3) === 0)
            ? "netstat -aon | findstr :$port"
            : "lsof -i :$port";

        exec($cmd, $output);

        return !empty($output);
    }
}
