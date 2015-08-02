<?php

namespace App\Console\Commands;

use App\Console\Controllers\ChatController;
use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Input\InputOption;

class ChatServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:start {port=9090}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts server listening.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $port = intval($this->argument('port'));

        $this->info("Starting chat web socket server on port " . $port);

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new ChatController()
                )
            ),
            $port
        );

        $server->run();

    }
}
