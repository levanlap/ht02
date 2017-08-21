<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Message;

class MessagePolicy
{
    /**
     * Intercept checks.
     *
     * @param User $currentUser
     * @return bool
     */
    public function before(User $currentUser)
    {
        if ($currentUser->tokenCan('admin')) {
            return true;
        }
    }

    /**
     * Determine if a given user has permission to show.
     *
     * @param User $currentUser
     * @param Message $message
     * @return bool
     */
    public function show(User $currentUser, Message $message)
    {
        return $currentUser->id === $message->userId;
    }

    /**
     * Determine if a given user can update.
     *
     * @param User $currentUser
     * @param Message $message
     * @return bool
     */
    public function update(User $currentUser, Message $message)
    {
        return $currentUser->id === $message->userId;
    }

    /**
     * Determine if a given user can delete.
     *
     * @param User $currentUser
     * @param Message $message
     * @return bool
     */
    public function destroy(User $currentUser, Message $message)
    {
        return $currentUser->id === $message->userId;
    }
}