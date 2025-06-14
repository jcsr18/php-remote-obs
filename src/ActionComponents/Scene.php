<?php

namespace Jcsr18\PhpRemoteObs\ActionComponents;

use Jcsr18\PhpRemoteObs\Enums\RequestType;
use Jcsr18\PhpRemoteObs\Types\Action;

class Scene extends ActionComponent
{
    public static function create(string $name): Action
    {
        return new Action(RequestType::CreateScene, [
            'sceneName' => $name,
        ]);
    }

    public static function remove(?string $sceneUuid = null, ?string $sceneName = null): Action
    {
        return new Action(RequestType::RemoveScene, [
            ...self::nameOrUuidValidate('sceneUuid', 'sceneName', $sceneUuid, $sceneName)
        ]);
    }

    public static function list(): Action
    {
        return new Action(RequestType::GetSceneList);
    }
}