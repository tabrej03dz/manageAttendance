<?php

namespace App\Services;

class UserDeviceService
{
    public static function detect(?string $userAgent): array
    {
        $userAgent = strtolower((string) $userAgent);

        return [
            'device_type' => self::deviceType($userAgent),
            'browser' => self::browser($userAgent),
            'platform' => self::platform($userAgent),
        ];
    }

    private static function deviceType(string $userAgent): string
    {
        if (
            str_contains($userAgent, 'ipad') ||
            str_contains($userAgent, 'tablet')
        ) {
            return 'Tablet';
        }

        if (
            str_contains($userAgent, 'mobile') ||
            str_contains($userAgent, 'android') ||
            str_contains($userAgent, 'iphone')
        ) {
            return 'Mobile';
        }

        return 'Desktop';
    }

    private static function browser(string $userAgent): string
    {
        if (str_contains($userAgent, 'edg/')) {
            return 'Microsoft Edge';
        }

        if (
            str_contains($userAgent, 'chrome/') &&
            !str_contains($userAgent, 'edg/')
        ) {
            return 'Google Chrome';
        }

        if (str_contains($userAgent, 'firefox/')) {
            return 'Mozilla Firefox';
        }

        if (
            str_contains($userAgent, 'safari/') &&
            !str_contains($userAgent, 'chrome/')
        ) {
            return 'Safari';
        }

        if (str_contains($userAgent, 'opera')) {
            return 'Opera';
        }

        return 'Unknown';
    }

    private static function platform(string $userAgent): string
    {
        if (str_contains($userAgent, 'windows')) {
            return 'Windows';
        }

        if (
            str_contains($userAgent, 'iphone') ||
            str_contains($userAgent, 'ipad')
        ) {
            return 'iOS';
        }

        if (str_contains($userAgent, 'android')) {
            return 'Android';
        }

        if (
            str_contains($userAgent, 'macintosh') ||
            str_contains($userAgent, 'mac os')
        ) {
            return 'macOS';
        }

        if (str_contains($userAgent, 'linux')) {
            return 'Linux';
        }

        return 'Unknown';
    }
}