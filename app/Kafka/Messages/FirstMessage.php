<?php

namespace App\Kafka\Messages;

use Aliftech\Kafka\Message\BaseMessage;
use App\Kafka\Topics\FirstTopic;

class FirstMessage extends BaseMessage
{

    protected $topic_class = FirstTopic::class;


    /**
     * Message variables to be transfered to Kafka. they should be public only.
     */
    public string $id;
    public string $username;
}
