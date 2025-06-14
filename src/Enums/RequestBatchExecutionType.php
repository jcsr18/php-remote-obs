<?php

namespace Jcsr18\PhpRemoteObs\Enums;

enum RequestBatchExecutionType: string
{
    case SerialRealtime = 'SerialRealtime';
    case SerialFrame = 'SerialFrame';
    case Parallel = 'Parallel';
}