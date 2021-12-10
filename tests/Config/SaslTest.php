<?php

namespace Aliftech\Kafka\Tests\Config;

use Aliftech\Kafka\Config\Sasl;
use Aliftech\Kafka\Tests\LaravelKafkaTestCase;

class SaslTest extends LaravelKafkaTestCase
{
    public function testGetUsername()
    {
        $sasl = new Sasl(
            username: 'username',
            password: 'password',
            mechanisms: 'mechanisms'
        );

        $this->assertEquals('username', $sasl->getUsername());
    }

    public function testGetPassword()
    {
        $sasl = new Sasl(
            username: 'username',
            password: 'password',
            mechanisms: 'mechanisms'
        );

        $this->assertEquals('password', $sasl->getPassword());
    }

    public function testGetMechanisms()
    {
        $sasl = new Sasl(
            username: 'username',
            password: 'password',
            mechanisms: 'mechanisms'
        );

        $this->assertEquals('mechanisms', $sasl->getMechanisms());
    }
}
