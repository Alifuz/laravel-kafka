<?php

namespace Aliftech\Kafka\Console\KafkaConsumer;

use Illuminate\Support\Arr;

class Options
{
    private array $topics;
    private ?string $groupId;
    private int $maxMessage;
    private array $config;
    private array $subscribe;

    public function __construct(array $options, array $config, array $subscribe)
    {
        if (is_string($options['topic'])) {
            $options['topic'] = [$options['topic']];
        }

        $this->config = $config;
        $this->topics = @$options['topic'];
        $this->groupId = @$options['groupId'];
        $this->maxMessage = @$options['maxMessage'] ?? -1;
        $this->subscribe = $subscribe;
    }

    public function getTopics(): array
    {
        $subTopics = $this->getSubscribableTopics();

        $topics = (is_array($this->topics) && ! empty($this->topics)) ? $this->topics : $subTopics;

        $topics = Arr::where($topics, function ($value) use ($subTopics) {
            return in_array($value, $subTopics);
        });

        return $topics;
    }

    public function getSecurityProtocol(): string {
        return $this->config['securityProtocol'] ?? "PLAINTEXT";
    }

    public function getSubscribableTopics(): array
    {
        $arr = [];
        foreach ($this->subscribe as $topic => $handler) {
            $arr[] = @get_class_vars($topic)['topic_key'];
        }
        return $arr;
    }

    public function getGroupId(): string
    {
        return (is_string($this->groupId) && strlen($this->groupId) > 1)
            ? $this->groupId
            : $this->config['groupId'];
    }


    public function getBrokers()
    {
        return $this->config['brokers'];
    }

    public function getDlqTopic(): ?string
    {
        return @$this->config['dlqTopic'];
    }

    public function getMaxMessage(): int
    {
        return (is_int($this->maxMessage) && $this->maxMessage >= 1) ? $this->maxMessage : -1;
    }
    public function getAutoCommit(): bool
    {
        return $this->config['autoCommit'];
    }
}
