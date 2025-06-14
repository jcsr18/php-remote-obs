## WIP

```php
<?php

use Jcsr18\PhpRemoteObs\ActionComponents\Input;
use Jcsr18\PhpRemoteObs\ActionComponents\Stream;
use Jcsr18\PhpRemoteObs\Client;
use Jcsr18\PhpRemoteObs\ActionComponents\Scene;
use Jcsr18\PhpRemoteObs\Contracts\ResponseListenerContract;
use Jcsr18\PhpRemoteObs\Enums\RequestBatchExecutionType;
use Jcsr18\PhpRemoteObs\Enums\RequestType;
use Revolt\EventLoop;

require_once __DIR__ . '/vendor/autoload.php';

class ResponseListener implements ResponseListenerContract
{
    public function success(string $requestId, RequestType $requestType, array $payload, ?string $requestBatchId = null): void
    {
        echo "[Success] {$requestType->name} ($requestId / $requestBatchId) -> success!" . PHP_EOL;
    }

    public function failed(string $requestId, RequestType $requestType, array $payload, int $errorCode, string $error, ?string $requestBatchId = null): void
    {
        echo "[Failed] {$requestType->name} ($requestId / $requestBatchId) -> $error ($errorCode)" . PHP_EOL;
    }
}

$client = new Client('IP', 4455, 'password');

$client->connect();

EventLoop::defer(function () use ($client) {
    $client->listen(new ResponseListener());
});

EventLoop::setErrorHandler(function ($e) {
    throw $e;
});

$client->createRequestStack(RequestBatchExecutionType::SerialFrame)
    ->addAction(Stream::setStreamKey('rtmp_common', 'rtmp://live.twitch.tv/app/', 'token'))
    ->addAction(Scene::remove('transmissao'))
    ->addAction(Input::remove(inputName: 'captura_chrome'))
    ->addAction(Input::remove(inputName: 'stream_video'))
    ->sleep(500)
    ->addAction(Scene::create('transmissao'))
    ->addAction(Input::createWindowCapture('captura_chrome', 'window', sceneName: 'transmissao'))
    ->addAction(Input::streamLocalFile('stream_video', 'full_path', sceneName: 'transmissao'))
    ->sleep(100)
    ->addAction(Stream::setActiveScene(sceneName: 'transmissao'))
    ->execute();

EventLoop::run();
```