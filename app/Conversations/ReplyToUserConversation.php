<?php

namespace App\Conversations;

use SergiX44\Nutgram\Conversations\Conversation;
use SergiX44\Nutgram\Nutgram;

class ReplyToUserConversation extends Conversation
{
    protected $forwardFrom;
    protected $messageText;
    protected $message;
    public function start(Nutgram $bot)
    {
        $this->message = $bot->message();
        $this->forwardFrom = $bot->message()->reply_to_message->forward_from->id;

        $this->messageText = $this->message->text;

        $bot->sendMessage($this->messageText, ['chat_id' => $this->forwardFrom]);
        $this->end();

    }
}
