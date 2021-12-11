# Laravel Kafka
[![Latest Version On Packagist](http://poser.pugx.org/aliftech/laravel-kafka/v)](https://packagist.org/packages/aliftech/laravel-kafka)
[![Total Downloads](http://poser.pugx.org/aliftech/laravel-kafka/downloads)](https://packagist.org/packages/aliftech/laravel-kafka)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)
[![PHP Version Require](http://poser.pugx.org/mateusjunges/laravel-kafka/require/php)](https://packagist.org/packages/mateusjunges/laravel-kafka)

Do you want to use kafka in your laravel projects? Most of the packages I've seen, does not provide an understandable syntax.

This package provides a nice way of producing (publishing) and consuming (subscribing & handling) kafka messages in your Laravel projects.

Follow these docs to install this package and start using kafka in your laravel projects.

- [Installation](#installation)
- [Configuration](#configuration)
- [Publishing Messages](#publishing-messages)
    - [Topics](#topics)
    - [Messages](#messages)
    - [Publishing](#publishing)
- [Subscribing To Topics](#subscribe-to-topics)
    - [Message Handlers](#message-handlers)
        - [Creating Handlers](#creating-handlers)
        - [Handling Logics](#handle-logics)
        - [Using Middlewares](#using-middlewares)
    - [Registering Handlers](#registering-handlers)
    - [Running Consumer](#running-consumer)
- [Run Consumer With Supervisor](#run-consumer-with-supervisor)
- [Naming Topics](#naming-topics)

# Installation
To install this package, you must have installed PHP RdKafka extension. First, follow the steps [here](https://github.com/edenhill/librdkafka#installation)
and install rdkafka library in your system and then install PHP RdKafka [here](https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/rdkafka.setup.html).

You may now install Laravel Kafka into your project using the Composer package manager:

```bash
composer require aliftech/laravel-kafka
```

After installing Laravel Kafka, publish its assets using the kafka:install Artisan command:

```text
php artisan kafka:install
```

# Configuration

After publishing Kafka's assets, its primary configuration file will be located at `config/kafka.php`.

# Publishing a message

A message can be published to a topic. To achieve that, you need to create a topic model and message [DTO](https://en.wikipedia.org/wiki/Data_transfer_object).

The concept of pub/sub in kafka is accomplished with Producers and Consumers. Producers will publish messages to topics and Consumers will subscribe to topics and listen for new messages. Topics are like events and messages are like DTO objects to deliver data from one source to many.

Messages should have the same data structures when they are sent to the same topic. To solve this problem, every message is created as a class with a data structure and can only be published to a unique topic.



## Topics

Topics are created in `./app/Kafka/Topics` folder with the following artisan command:

```bash
php artisan make:topic MyTopic
```

After the generation of the topic, you have to set the corresponding kafka topic key and here it's `my_topic`:

```php
/**
 * Put down the correct Topic Key.
 *
 * @var string $topic_key
 */
public static string $topic_key = 'my_topic';
```

## Messages

Messages are created in `./app/Kafka/Messages` folder with the following artisan command:

```bash
php artisan make:message MyMessage
```

And now, you have to connect to a corresponding topic:

```php
/**
 * Topic that the message will be sent to.
 */
protected ?string $topic_class = MyTopic::class;
```

After this, DTO properties should be set up in the class:

```php
/**
 * Message variables to be transfered to Kafka.
 * they should be public only.
 */
public int $id;
public string $name;
public string $file_url;
public bool $is_checked;
```

In addition, you could also create a constuctor function to set the properties when creating the object:

```php
public function __contruct(int $id, string $name, string $file_url, bool $is) {
    $this->id = $id;
    $this->name = $name;
    $this->file_url = $file_url;
    $this->is_checked = $is_checked;
}
```

## Publishing

In order to publish the message to the topic, `publish()` method should be called on the message object:

```php
use App\Kafka\Messages\MyMessage;

// you can create a message object
$message = MyMessage::create(1, 'My name changed', 'file2.jpg', false);
// or
$message = new MyMessage(1, 'My name changed', 'file2.jpg', false);

// you can set properties like this too
$message->id = 1;
$message->name = 'My name changed';
$message->file_url = 'file2.jpg';
$message->is_checked = false;

// now that message object is created and
// filled with data, it's ready to be published
$message->publish();
```

Now, Your message (`MyMessage`) has been sent to the topic (`MyTopic`).