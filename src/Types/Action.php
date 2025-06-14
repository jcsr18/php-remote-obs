<?php

namespace Jcsr18\PhpRemoteObs\Types;


use Jcsr18\PhpRemoteObs\Enums\RequestType;

class Action
{
    public function __construct(
        public RequestType $type,
        public array       $payload = []
    ){}
}