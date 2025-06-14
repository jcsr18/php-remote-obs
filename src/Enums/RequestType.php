<?php

namespace Jcsr18\PhpRemoteObs\Enums;

enum RequestType: string
{
    case StartStream = 'StartStream';
    case StopStream = 'StopStream';
    case SetStreamSettings = 'SetStreamServiceSettings';
    case CreateScene = 'CreateScene';
    case GetSceneList = 'GetSceneList';
    case SetCurrentProgramScene = 'SetCurrentProgramScene';
    case RemoveScene = 'RemoveScene';
    case GetSourceActive = 'GetSourceActive';
    case GetSourceScreenshot = 'GetSourceScreenshot';
    case GetCurrentProgramScene = 'GetCurrentProgramScene';
    case CreateInput = 'CreateInput';
    case RemoveInput = 'RemoveInput';
    case Sleep = 'Sleep';
}
