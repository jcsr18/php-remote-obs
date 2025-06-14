<?php

namespace Jcsr18\PhpRemoteObs\Contracts;

use Jcsr18\PhpRemoteObs\Enums\RequestType;

interface ResponseListenerContract
{
    public function success(string $requestId, RequestType $requestType, array $payload, ?string $requestBatchId = null): void;

    public function failed(string $requestId, RequestType $requestType, array $payload, int $errorCode, string $error, ?string $requestBatchId = null): void;
}