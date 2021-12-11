# Laravel Kafka
[![Latest Version On Packagist](http://poser.pugx.org/aliftech/laravel-kafka/v)](https://packagist.org/packages/aliftech/laravel-kafka)
[![Total Downloads](http://poser.pugx.org/aliftech/laravel-kafka/downloads)](https://packagist.org/packages/aliftech/laravel-kafka)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE)
[![PHP Version Require](http://poser.pugx.org/mateusjunges/laravel-kafka/require/php)](https://packagist.org/packages/mateusjunges/laravel-kafka)

Do you want to use kafka in your laravel projects? Most of the packages I've seen, does not provide an understandable syntax.

This package provides a nice way of producing (publishing) and consuming (subscribing & handling) kafka messages in your Laravel projects.

Follow these docs to install this package and start using kafka in your laravel projects (Docs might be incomplete. work in progress!).

- [Installation](#installation)
- [Configuration](#configuration)

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