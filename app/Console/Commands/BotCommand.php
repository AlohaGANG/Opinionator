<?php

namespace App\Console\Commands;

use App\Conversations\FeedBackConversation;
use App\Conversations\ReplyToUserConversation;
use App\Models\Chat;
use Illuminate\Console\Command;
use SergiX44\Nutgram\Conversations\Conversation;
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

            FeedBackConversation::begin($bot);
        })->description('Start Command');

        $bot->onMessage(function (Nutgram $bot) {
            $this->message = $bot->message();
            $chatId = Chat::latest()->value('chat_id');
            if ($this->message->reply_to_message !== null && $this->message->chat->id == $chatId){

                ReplyToUserConversation::begin($bot);
            }
        });
        $bot->onMyChatMember(function (Nutgram $bot) {

            if ($bot->chat()->isGroup() && $bot->update()->my_chat_member->new_chat_member->user->id === $bot->getMe()->id){

                $chatId = $bot->update()->my_chat_member->chat->id;
                $chatTitle = $bot->update()->my_chat_member->chat->title;

                Chat::updateOrCreate([
                    'chat_id' => $chatId,
                    'title' => $chatTitle,
                ]);
            }
        });
        $bot->onLeftChatMember(function (Nutgram $bot) {

            if ($bot->chat()->isGroup() && $bot->message()->left_chat_member->id === $bot->getMe()->id) {
                $oldChatId = $bot->message()->chat->id;
                Chat::where('chat_id', $oldChatId)->delete();
            }
        });
        $bot->run();
    }
}
