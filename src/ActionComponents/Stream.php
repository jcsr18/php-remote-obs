<?php

namespace Jcsr18\PhpRemoteObs\ActionComponents;

use Jcsr18\PhpRemoteObs\Enums\RequestType;
use Jcsr18\PhpRemoteObs\Types\Action;

class Stream extends ActionComponent
{
    public static function setStreamKey(string $serviceType, string $server, string $key): Action
    {
        return new Action(RequestType::SetStreamSettings, [
            'streamServiceType' => $serviceType,
            'streamServiceSettings' => [
                'server' => $server,
                'key' => $key,
            ],
        ]);
    }

    public static function startStream(): Action
    {
        return new Action(RequestType::StartStream);
    }

    public static function stopStream(): Action
    {
        return new Action(RequestType::StopStream);
    }

    public static function getCurrentScene(): Action
    {
        return new Action(RequestType::GetCurrentProgramScene);
    }

    public static function setActiveScene(?string $sceneUuid = null, ?string $sceneName = null): Action
    {
        return new Action(RequestType::SetCurrentProgramScene, [
            ...self::nameOrUuidValidate('sceneUuid', 'sceneName', $sceneUuid, $sceneName)
        ]);
    }

    public static function getSourceActive(): Action
    {
        return new Action(RequestType::GetSourceActive);
    }
}