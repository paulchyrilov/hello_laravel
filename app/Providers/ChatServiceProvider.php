<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ChatServiceProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind("chat.emitter", function()
        {
            return new EventEmitter();
        });
        $this->app->bind("chat.chat", function()
        {
            return new Chat(
                $this->app->make("chat.emitter")
            );
        });
        $this->app->bind("chat.user", function()
        {
            return new User();
        });
        $this->app->bind("chat.command.serve", function()
        {
            return new Command\Serve(
                $this->app->make("chat.chat")
            );
        });
        $this->commands("chat.command.serve");
    }

    public function provides()
    {
        return [
            "chat.chat",
            "chat.command.serve",
            "chat.emitter",
            "chat.server"
        ];
    }
}
