<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RadarServe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'radar:serve {--port=8000 : The port to serve the application on} {--ip= : The IP address to serve the application on (defaults to local network IP)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the Laravel server using the local IP address, along with Reverb and Queue worker concurrently.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $port = $this->option('port');
        $ip = $this->option('ip') ?: $this->getLocalIp();

        $this->info("🚀 Starting local server with IP: {$ip}");
        $this->info("   Local server url: http://{$ip}:{$port}");
        $this->info("   Starting Reverb Socket Server...");
        $this->info("   Starting Queue Worker...");
        $this->info("   Press Ctrl+C to stop all services.");

        // Usaremos Symfony Process nativo en lugar de npx concurrently para evitar problemas de EPIPE en Windows
        $processes = [
            'server' => new \Symfony\Component\Process\Process(['php', 'artisan', 'serve', "--host={$ip}", "--port={$port}"]),
            'reverb' => new \Symfony\Component\Process\Process(['php', 'artisan', 'reverb:start']),
            'queue'  => new \Symfony\Component\Process\Process(['php', 'artisan', 'queue:work', '--queue=broadcasts,default']),
        ];

        $colors = [
            'server' => "\033[34m", // Azul
            'reverb' => "\033[35m", // Morado
            'queue'  => "\033[31m", // Rojo
        ];
        $reset = "\033[0m";

        foreach ($processes as $process) {
            $process->setTimeout(null);
            $process->start();
        }

        while (true) {
            foreach ($processes as $name => $process) {
                $color = $colors[$name];
                
                $output = $process->getIncrementalOutput();
                if (!empty(trim($output))) {
                    foreach (explode("\n", rtrim($output)) as $line) {
                        echo "{$color}[{$name}]{$reset} {$line}\n";
                    }
                }

                $errorOutput = $process->getIncrementalErrorOutput();
                if (!empty(trim($errorOutput))) {
                    foreach (explode("\n", rtrim($errorOutput)) as $line) {
                        echo "{$color}[{$name}] [ERR]{$reset} {$line}\n";
                    }
                }
            }
            usleep(100000); // 100ms
        }
    }

    /**
     * Get the machine's local IP address logically by establishing a dummy UDP socket.
     * 
     * If the sockets extension is disabled, falls back to hostname resolution.
     *
     * @return string
     */
    protected function getLocalIp(): string
    {
        if (function_exists('socket_create')) {
            $sock = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            if ($sock !== false) {
                @socket_connect($sock, "8.8.8.8", 53);
                socket_getsockname($sock, $name);
                socket_close($sock);

                if ($name) {
                    return $name;
                }
            }
        }

        return gethostbyname(gethostname());
    }
}
