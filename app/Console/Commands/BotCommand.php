<?php

namespace App\Console\Commands;

use App\Conversations\FeedBackConversation;
use Illuminate\Console\Command;
use SergiX44\Nutgram\Logger\ConsoleLogger;
use SergiX44\Nutgram\Nutgram;

class BotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Telegram bot started command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Nutgram $bot)
    {
        $bot = new Nutgram($_ENV['TELEGRAM_TOKEN'],[
            'timeout' => $_ENV['CONNECT_TIMEOUT'],
            'logger' => ConsoleLogger::class]);

        $bot->onCommand('start', function (Nutgram $bot) {
            $bot->sendMessage('Hello');
        })->description('Start Command');
        $bot->onCommand('feedBack', function (Nutgram $bot) {
            FeedBackConversation::begin($bot);
        })->description('Feed Back Command');


        $bot->run();
    }
}
