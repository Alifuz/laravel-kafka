<?php

use Aliftech\Kafka\Facades\Kafka;
use Aliftech\Kafka\Message\Message;
use App\Kafka\Messages\FirstMessage;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/publish', function () {

// $message = new Message(
//     headers: ['header-key' => 'header-value'],
//     body: ['key' => 'value'],
//     key: 'kafka key here'
// );

// dd(Kafka::publishOn('topic')->withMessage($message)->send());
$message = new FirstMessage(12, 'my_username it is');
    dd($message->publish());
});
