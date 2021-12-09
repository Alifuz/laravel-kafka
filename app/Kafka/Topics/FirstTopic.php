<?php

namespace App\Kafka\Topics;

use Aliftech\Kafka\Contracts\Topicable;

class FirstTopic implements Topicable
{
    /**
     * Put down the correct Topic Key.
     *
     * @var string $topic_key
     */
    public static $topic_key = 'first_topic';

}
