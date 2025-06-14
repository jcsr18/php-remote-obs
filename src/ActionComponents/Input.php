<?php

namespace Jcsr18\PhpRemoteObs\ActionComponents;

use Jcsr18\PhpRemoteObs\Enums\InputKind;
use Jcsr18\PhpRemoteObs\Enums\RequestType;
use Jcsr18\PhpRemoteObs\Types\Action;

class Input extends ActionComponent
{
    public static function createWindowCapture(string $name, string $window, bool $captureCursor = true, bool $compatibility = false, bool $enabled = true, ?string $sceneUuid = null, ?string $sceneName = null): Action
    {
        $scene = self::nameOrUuidValidate('sceneUuid', 'sceneName', $sceneUuid, $sceneName);

        return new Action(RequestType::CreateInput, [
            ...$scene,
            'inputKind' => InputKind::WindowCapture,
            'inputName' => $name,
            'inputSettings' => [
                'window' => $window,
                'capture_cursor' => $captureCursor,
                'compatibility' => $compatibility,
            ],
            'sceneItemEnabled' => $enabled,
        ]);
    }

    public static function streamLocalFile(string $name, string $fullPath, bool $looping = true, int $speed = 100, bool $clearOnEnd = true, $hwDecode = false, $enabled = true, ?string $sceneUuid = null, ?string $sceneName = null): Action
    {
        $scene = self::nameOrUuidValidate('sceneUuid', 'sceneName', $sceneUuid, $sceneName);

        return new Action(RequestType::CreateInput, [
            ...$scene,
            'inputKind' => InputKind::FfmpegSource,
            'inputName' => $name,
            'inputSettings' => [
                'local_file' => $fullPath,
                'looping' => $looping,
                'speed_percent' => $speed,
                'clear_on_media_end' => $clearOnEnd,
                'hw_decode' => $hwDecode,
            ],
            'sceneItemEnabled' => $enabled,
        ]);
    }

    public static function remove(?string $inputUuid = null, ?string $inputName = null): Action
    {
        return new Action(RequestType::RemoveInput, [
            ...self::nameOrUuidValidate('inputUuid', 'inputName', $inputUuid, $inputName)
        ]);
    }
}