<?php

namespace Jcsr18\PhpRemoteObs\ActionComponents;

abstract class ActionComponent
{
    protected static function nameOrUuidValidate(string $uuidField, string $nameField, ?string $uuidValue = null, ?string $nameValue = null): array
    {
        if (! $uuidValue && ! $nameValue) {
            throw new \InvalidArgumentException("Must have $uuidField or $nameField");
        }

        return array_filter([
            $uuidField => $uuidValue,
            $nameField => $nameValue,
        ]);
    }
}