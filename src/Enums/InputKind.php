<?php

namespace Jcsr18\PhpRemoteObs\Enums;

enum InputKind: string
{
    CASE WindowCapture = 'window_capture';
    CASE MonitorCapture = 'monitor_capture';
    CASE DshowInput = 'dshow_input';
    CASE FfmpegSource = 'ffmpeg_source';
    CASE ImageSource = 'image_source';
    CASE TextGdiplus = 'text_gdiplus';
    CASE BrowserSource = 'browser_source';
    CASE GameCapture = 'game_capture';
}