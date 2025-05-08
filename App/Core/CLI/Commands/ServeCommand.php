<?php 
namespace App\Core\CLI\Commands;

use App\Core\CLI\System\Out;
use App\Core\Contract\CommandInterface;

class ServeCommand implements CommandInterface {

    private string $host; 
    private string $port; 
    public function exe(array $command): void
    {
        $this->host = $command[2] ?? '127.0.0.2';
        $this->port = $command[3] ?? '8000';
        $dir = getcwd();
    Out::info($dir);

        if (!is_dir($dir)) {
            Out::error(" index.php  not found. Please check your project");
        }

        if (!file_exists($dir . '/index.php')) {
            Out::error("Missing 'index.php' in the public directory.");
        }

        if ($this->isPortInUse($this->port)) {
            Out::warn("⚠️  Port $this->port is already in use. Try another port.");
            exit(1);
        }

        Out::ok("Starting development server...");
        Out::ln("🌐 Listening on: http://$this->host:$this->port");
        Out::ln("📁 Serving directory: $dir");
        Out::ln("🛑 Press Ctrl+C to stop the server\n");

        passthru("php -S $this->host:$this->port");
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
