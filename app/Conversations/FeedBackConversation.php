<?php

namespace App\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

class FeedBackConversation extends Conversation
{
    public $chatId;
    public $messageId;
    public function start(Nutgram $bot)
    {
        $bot->sendMessage('Введите свой отзыв');
        $this->next('secondStep');
    }

    public function secondStep(Nutgram $bot)
    {
        $this->messageId = $bot->message()->message_id;
        $this->chatId = $bot->message()->chat->id;
        $bot->sendMessage('Отлично.Ждите ответа');
        $bot->forwardMessage(-855697254, "$this->chatId", "$this->messageId");
        $this->end();
    }

//    public function thirdStep(Nutgram $bot)
//    {
//
//        $this->end();
//    }

}
