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
    - [Concept of Producing Messages](#concept-of-producing-messages)
    - [Topics](#topics)
    - [Messages](#messages)
    - [Publishing](#publishing)
- [Subscribing To Topics](#subscribe-to-topics)
    - [Concept of Consuming Topics](#concept-of-consuming-topics)
    - [Message Handlers](#message-handlers)
        - [Creating Handlers](#creating-handlers)
        - [Handling Logics](#handling-logics)
        - [Using Middlewares](#using-middlewares)
    - [Registering Handlers](#registering-handlers)
    - [Running Consumer](#running-consumer)
- [Run Consumer With Supervisor](#run-consumer-with-supervisor)
- [Concept of Naming Topics](#concept-of-naming-topics)

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

After publishing Kafka's assets, its primary configuration file will be located at `config/kafka.php`. (More here later)

# Publishing Messages

A message can be published to a topic. To achieve that, you need to create a topic model and a message [DTO](https://en.wikipedia.org/wiki/Data_transfer_object).

## Concept of Producing messages

The concept of pub/sub in kafka is accomplished with Producers and Consumers. Producers will publish messages to topics and Consumers will subscribe to topics and listen for new messages. Topics are like events and messages are like DTO objects to deliver data from one source to other.

Messages should have the same data structures when they are sent to the same topic. To solve this problem, your message should be created as a separate class with a data structure and it can only be published to a unique topic. Meaning that, single Topic should NOT receive two messages with different data structures.

Think of Topics as tables (in database). You cannot insert two records with different column structures into the table. And think of the records as your messages. You can send 2 messages with different data structure to one topic. But, this will mostly create problems instead of solving them!

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
protected string $topic_class = MyTopic::class;
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

<!-- meta data -->

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

# Subscribing To Topics

A topic can be consumed like an event can be listened. And Consumers run certain handlers when there is a new message published to the topic.

## Concept of Consuming Topics

To use Consumers, you need to create Topic model and Handlers. And then, you should attach handlers to topics. You can handle new messages inside your handlers. After sutting up topics and handlers, Artisan command `kafka:consume` can be run to subscribe to topics. This command will automatically run your handlers in exact order as you provided when there is a new message.

## Message Handlers

Message handlers are created as separate classes to handle messages.

### Creating Handlers

Handlers are created in `./app/Kafka/Handlers` folder with the following artisan command:

```bash
php artisan make:handler MyHandler
```

### Handling Logics

After generating the handler (`MyHandler`), you should put your logic in `handle` function of the handler:

```php
/**
 * Handle the topic message.
 *
 * @param $message
 * @return void
 */
public function handle($message): void
{
    // handle new messages here. Put your logic here
}
```

### Using Middlewares

Additionally, if you want to filter messages even before the message gets to the function `handle`, you should use middewares in handlers. For this purpose, you should use a special function called `middleware`:

```php
/**
 * Middleware to be passed before handling the message
 *
 * @param $message
 * @return void
 */
public function middleware($message, Closure $next): void
{
    // here put your filters
    $next($message);
}
```

Call the second arg `$next` like a function with the message inside if you want the middleware to pass and call the `handle` function. Don't call the `$next` if `$message` cannot pass your filters or something like that. When you call `$next`, be sure to pass `$message` to the function like `$next($message)`.

Cases when you might want to use middlewares are a lot. One of the Cases is that if you want to use multiple handlers to subscribe for a single topic. You might want the new messages (coming to a single topic) to be processed by multiple hanlers and each handler might filter the messages by their meta data (in the message headers).

## Registering Handlers

You have created your topics and your handlers. Now, you should set which handlers to be called when a new message is published to a certain topic. And it's set inside the service provider (called `KafkaServiceProvider`) which is published when you called Artisan command `kafka:install`:

```php
use App\Kafka\Topics\MyTopic;
use App\Kafka\Handlers\MyHandler;

/**
 * The topic consumer mappings for the application.
 *
 * @var array
 */
protected $subscribe = [
    MyTopic::class => [
        MyHandler::class,
        // MyHandler2::class,
        // MyHandler3::class,
        // ...
        // You could put a lot of handlers here
    ],
];
```

## Consuming Topics

Now, You have set the relationship between topics and handlers. To continuesly process new message, You should call the following Artisan command:

```bash
php artisan kafka:consume
```

This command will keep running and be listening to new messages. But when this stops, your project will also stop handling new messages. But, kafka is intelligent and it will save your consumer's offset. And when you call the Artisan command again, your consumer will start from that offset where it stops working.

** Don't run this Artisan command on production without process managers, because, it might stop working. Using Supervisor is recommended!

# Run Consumer With Supervisor

Here will be docs soon!

# Concept of Naming Topics

Here will be docs soon!
