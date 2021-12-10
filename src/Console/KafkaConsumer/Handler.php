<?php

namespace Aliftech\Kafka\Console\KafkaConsumer;

use Aliftech\Kafka\AbstractHandler;

class Handler
{
    private array $subscribe;

    private array $relations;

    function __construct($subscribe)
    {
        $this->subscribe = $subscribe;
        $arr = [];
        foreach ($this->subscribe as $topic => $handler) {
            $arr[@get_class_vars($topic)['topic_key']] = $topic;
        }
        $this->relations = $arr;
    }


    public function getRelation(): array
    {
        return $this->relations;
    }

    /**
     * Return Handlers based on a topic name
     *
     * @param string $topic
     * @return string[]
     */
    public function getHandler(string $topic): array
    {
        return @$this->subscribe[@$this->getRelation()[$topic]] ?? [];
    }

    public function handle(\Aliftech\Kafka\Contracts\KafkaConsumerMessage $message) {
        $handlers = $this->getHandler($message->getTopicName());

        foreach ($handlers as $class) {
            /**
             * @var AbstractHandler $handler
             */
            $handler = new $class;

            $handler->middleware($message, function ($message) use ($handler) {
                $handler->handle($message);
            });
        }
    }
}
