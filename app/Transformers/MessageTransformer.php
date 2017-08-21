<?php

namespace App\Transformers;

use App\Models\Message;
use League\Fractal\TransformerAbstract;

class MessageTransformer extends TransformerAbstract
{
    public function transform(Message $message)
    {
        return [
            'id'        => $message->uid,
            'userId'    => $message->userId,
            'subject'   => $message->subject,
            'message'   => $message->message,
            'createdAt' => (string) $message->created_at,
            'updatedAt' => (string) $message->updated_at,
        ];
    }
}