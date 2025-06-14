<?php

namespace Jcsr18\PhpRemoteObs;

use Jcsr18\PhpRemoteObs\Enums\RequestType;
use Jcsr18\PhpRemoteObs\Enums\RequestBatchExecutionType;
use Jcsr18\PhpRemoteObs\Types\Action;

class RequestStack
{
    private array $requests = [];

    public function __construct(
        public Client                    $socket,
        public RequestBatchExecutionType $executionType,
        public bool                      $stopOnFail = false,
    ) {}

    public function addAction(Action $action, ?string $actionId = null): self
    {
        $this->addRequest(
            $action->type,
            is_null($actionId) ? Util::generateRequestId('action-') : $actionId,
            $action->payload,
        );

        return $this;
    }

    public function execute(?string $requestId = null): void
    {
        $this->socket->sendBatchRequest(
            $this->executionType,
            is_null($requestId) ? Util::generateRequestId('batch-') : $requestId,
            $this->requests,
            $this->stopOnFail,
        );
    }

    public function sleep(int $ms, ?string $requestId = null): self
    {
        $this->requests[] = [
            'requestId' => is_null($requestId) ? Util::generateRequestId('sleep-') : $requestId,
            'requestType' => RequestType::Sleep,
            'requestData' => [
                'sleepMillis' => $ms,
            ],
        ];

        return $this;
    }

    protected function addRequest(RequestType $type, string $requestId, array $payload = []): self
    {
        $this->requests[] = [
            'requestId' => $requestId,
            'requestType' => $type,
            'requestData' => $payload,
        ];

        return $this;
    }
}