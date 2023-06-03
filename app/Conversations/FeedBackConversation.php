<?php

namespace App\Conversations;

use App\Models\Chat;
use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

class FeedBackConversation extends Conversation
{
    protected ?string $feedback;
    protected bool $success = false;
    protected int $chat_id;
    protected int $message_id;
    protected $chatID;
    public function start(Nutgram $bot)
    {
        $message = $bot->sendMessage('Введите свой отзыв', [
            'reply_markup' => ReplyKeyboardMarkup::make(resize_keyboard: true)
                ->addRow(KeyboardButton::make('Отменить'))
        ]);
        $this->chat_id = $message->chat->id;
        $this->message_id = $message->message_id;

        $this->next('getFeedback');

    }

    public function getFeedback(Nutgram $bot) : void
    {
        if ($bot->message()?->text === 'Отменить'){
            $this->end();
            return;
        }
        if ($bot->message()?->text === null){
            $bot->sendMessage('Неверный отзыв.');
            $this->start($bot);

            return;
        }

        $this->messageId = $bot->message()->message_id;
        $this->chatId = $bot->message()->chat->id;
        $this->chatID = Chat::latest()->value('chat_id');
        $bot->forwardMessage($this->chatID, "$this->chatId", "$this->messageId");
        $this->success = true;
        $this->end();
    }
    public function closing(Nutgram $bot)
    {
        $bot->deleteMessage($this->chat_id, $this->message_id);
        if ($this->success) {
            $bot->sendMessage('Отлично.Ждите ответа');

            return;
        }
        $bot->sendMessage('Отмененно');
    }

}
