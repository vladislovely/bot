<?php

namespace App\Console\Commands;

use App\Telegram\Commands\StartCommand;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class InitializeStartCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:initialize-start-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize telegram start command';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Telegram::addCommand(StartCommand::class);

        $this->info('Telegram command initialize successful');
    }
}
