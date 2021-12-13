<?php

use Illuminate\Support\Str;

return [

    /**
     * Your kafka brokers url.
     */
    'brokers' => env('KAFKA_BROKERS', 'localhost:9092'),

    /**
     * Kafka consumers belonging to the same consumer group share a group id.
     * The consumers in a group then divides the topic partitions as fairly amongst themselves as possible by
     * establishing that each partition is only consumed by a single consumer from the group.
     * This config defines the consumer group id you want to use for your project.
     */
    'consumer_group_id' => env('KAFKA_CONSUMER_GROUP_ID', 'laravel_' . Str::slug(env('APP_NAME')) . '_consumer'),

    /**
     * Security protocol for authenticating with kafka brokers
     *
     * Possible values:
     * PLAINTEXT - Un-authenticated, non-encrypted channel
     * SASL_PLAINTEXT - SASL authenticated, non-encrypted channel
     * SASL_SSL - SASL authenticated, SSL channel
     * SSL - SSL channel
     */
    'security_protocol' => env('KAFKA_SECURITY_PROTOCOL', 'PLAINTEXT'),

    /**
     * These options will help when kafka has a security protocol of SASL authentication.
     * To authenticate into kafka, correct username passwords should be set. For now,
     * only PLAIN SASL mechanism is supported!
     */
    'sasl' => [
        'username' => env('KAFKA_SASL_USERNAME', ''),
        'password' => env('KAFKA_SASL_PASSWORD', ''),
        'mechanisms' => 'PLAIN',
    ],

    /**
     * After the consumer receives its assignment from the coordinator,
     * it must determine the initial position for each assigned partition.
     * When the group is first created, before any messages have been consumed, the position is set according to a configurable
     * offset reset policy (auto.offset.reset). Typically, consumption starts either at the earliest offset or the latest offset.
     * You can choose between "latest", "earliest" or "none".
     */
    'offset_reset' => env('KAFKA_OFFSET_RESET', 'earliest'),

    /*
     * If you set enable.auto.commit (which is the default), then the consumer will automatically commit offsets periodically at the
     * interval set by auto.commit.interval.ms.
     */
    'auto_commit' => env('KAFKA_AUTO_COMMIT', true),

    /**
     * Kafka supports 4 compression codecs: none , gzip , lz4 and snappy
     */
    'compression' => env('KAFKA_COMPRESSION_TYPE', 'snappy'),

    /**
     * Choose if debug is enabled or not.
     */
    'debug' => env('KAFKA_DEBUG', false),
];
