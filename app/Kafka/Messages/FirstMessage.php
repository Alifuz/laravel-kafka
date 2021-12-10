<?php

namespace App\Kafka\Messages;

use Aliftech\Kafka\Message\BaseMessage;
use App\Kafka\Topics\FirstTopic;

class FirstMessage extends BaseMessage
{
    /**
     * Message defualt configs. they should be protected only.
     */
    protected ?string $_topic = FirstTopic::class;
    protected ?string $_message_key = 'key';

    /**
     * Message variables to be transfered to Kafka. they should be public only.
     */
    public string $id;
    public string $username;

    /**
     * Contruct a new message and set props here
     *
     * @return void
     */
    public function __construct(int $id, string $username)
    {
        $this->id = $id;
        $this->username = $username;
    }

}
