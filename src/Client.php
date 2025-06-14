<?php

namespace Jcsr18\PhpRemoteObs;

use Amp\Websocket\Client\WebsocketConnection;
use Jcsr18\PhpRemoteObs\Contracts\ResponseListenerContract;
use Jcsr18\PhpRemoteObs\Enums\RequestType;
use Jcsr18\PhpRemoteObs\Enums\RequestBatchExecutionType;
use Jcsr18\PhpRemoteObs\Enums\ResponseType;
use function Amp\Websocket\Client\connect;

class Client
{
    protected WebsocketConnection $connection;

    protected ?ResponseListenerContract $responseListener = null;

    private string $challenge;

    private string $salt;

    private int $rpcVersion;

    public function __construct(
        public readonly string $host,
        public readonly int | string $port,
        private readonly ?string $password,
    ) {}

    public function getConnectionUri(): string
    {
        return "ws://$this->host:$this->port";
    }

    public function setResponseListener(ResponseListenerContract $responseListener): ResponseListenerContract
    {
        $this->responseListener = $responseListener;

        return $this->responseListener;
    }

    public function connect(): self
    {
        $this->connection = connect($this->getConnectionUri());

        while ($message = $this->connection->receive()) {
            ['op' => $type,  'd' => $payload] = $this->decode($message->buffer());

            if (ResponseType::convert($type) === ResponseType::Hello) {
                $this->responseHello($payload);
                continue;
            }

            break;
        }

        return $this;
    }

    public function listen(ResponseListenerContract $responseListener): void
    {
        $this->setResponseListener($responseListener);

        while ($message = $this->connection->receive()) {
            ['op' => $type,  'd' => $payload] = $this->decode($message->buffer());

            match (ResponseType::convert($type)) {
                ResponseType::Identified => $this->responseIdentified($payload),
                ResponseType::Reidentify => $this->identify(),
                ResponseType::RequestResponse => $this->processResponse($payload),
                ResponseType::RequestBatchResponse => $this->processBatchResponse($payload),
                default => var_dump('Não tratado: '. json_encode($payload) . PHP_EOL),
            };
        }
    }

    public function sendRequest(RequestType $type, string $requestId, array $payload = []): void
    {
        $this->send(ResponseType::Request,
            [
                'requestType' => $type,
                'requestId' => $requestId,
                'requestData' => $payload,
            ],
        );
    }

    public function sendBatchRequest(RequestBatchExecutionType $executionType, string $requestBatchId, array $requests, $haltOnFailure = false): void
    {
        $this->send(ResponseType::RequestBatch,
            [
                'requestType' => $executionType,
                'requestId' => $requestBatchId,
                'haltOnFailure' => $haltOnFailure,
                'requests' => $requests,
            ],
        );
    }

    public function createRequestStack(RequestBatchExecutionType $executionType): RequestStack
    {
        return new RequestStack($this, $executionType);
    }

    protected function responseHello(array $payload): void
    {
        $this->challenge = $payload['authentication']['challenge'];
        $this->salt = $payload['authentication']['salt'];
        $this->rpcVersion = $payload['rpcVersion'];

        $this->identify();
    }

    protected function responseIdentified(array $payload): void
    {
        $this->rpcVersion = $payload['negotiatedRpcVersion'];
    }

    protected function identify(): void
    {
        $this->send(ResponseType::Identify, [
            'authentication' => Util::generateObsAuth($this->salt, $this->challenge, $this->password)
        ]);
    }

    protected function processResponse(array $payload, ?string $batchRequestId = null): void
    {
        $type = RequestType::from($payload['requestType']);
        $requestId = $payload['requestId'];
        $requestStatus = $payload['requestStatus'];
        $responseData = $payload['responseData'] ?? [];

        $payload['requestStatus']['result'] === true ?
            $this->responseListener->success($requestId, $type, $responseData, $batchRequestId) :
            $this->responseListener->failed($requestId, $type, $responseData, $requestStatus['code'], $requestStatus['comment'], $batchRequestId);
    }

    protected function processBatchResponse(array $batch): void
    {
        foreach ($batch['results'] as $payload) {
            $this->processResponse($payload, $batch['requestId']);
        }
    }

    private function send(ResponseType $type, array $payload): void
    {
        $this->connection->sendText($this->encode([
            'op' => $type->value,
            'd' => [
                'rpcVersion' => $this->rpcVersion,
                ...$payload,
                'eventSubscriptions' => 33,
            ],
        ]));
    }

    private function decode(string $payload): array
    {
        return json_decode($payload, true);
    }

    private function encode(array $payload): string
    {
        return json_encode($payload);
    }
}
