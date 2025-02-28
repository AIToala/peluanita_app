<?php

namespace App\Utils;

final class LogUtils
{
    public static function error($message, array $context = []): void
    {
        self::execute('error', $message, $context);
    }

    public static function info($message, array $context = []): void
    {
        self::execute('info', $message, $context);
    }

    public static function debug($message, array $context = []): void
    {
        self::execute('debug', $message, $context);
    }

    public static function warn($message, array $context = []): void
    {
        self::execute('warning', $message, $context);
    }

    public static function emergency($message, array $context = []): void
    {
        self::execute('emergency', $message, $context);
    }

    private static function execute($function, $message, array $context): void
    {
        try {
            if (env('APP_ENV', 'local') == 'local') {
                \Log::{$function}($message, $context);

                return;
            }
            //\Log::channel('mongodbquerylog')->{$function}($message, $context);
        } catch (\Throwable $th) {
            \Log::error($th);
        }
    }
}
