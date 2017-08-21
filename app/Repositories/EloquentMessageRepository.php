<?php

namespace App\Repositories;

use App\Models\Message;
use App\Repositories\Contracts\MessageRepository;

class EloquentMessageRepository extends AbstractEloquentRepository implements MessageRepository
{
    /**
     * Model name.
     *
     * @var string
     */
    protected $modelName = Message::class;
}