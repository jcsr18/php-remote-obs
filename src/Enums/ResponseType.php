<?php

namespace Jcsr18\PhpRemoteObs\Enums;

enum ResponseType: int
{
    case Hello = 0;
    case Identify = 1;
    case Identified = 2;
    case Reidentify = 3;
    case Event = 5;
    case Request = 6;
    case RequestResponse = 7;
    case RequestBatch = 8;
    case RequestBatchResponse = 9;

    public static function convert(int $v): self
    {
        return self::from($v);
    }
}
