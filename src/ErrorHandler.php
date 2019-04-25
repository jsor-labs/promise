<?php

namespace React\Promise;

final class ErrorHandler
{
    private static $handlers = [];

    public static function set(?callable $handler): ?callable
    {
        $previous = self::$handlers[0] ?? null;

        \array_unshift(self::$handlers, $handler);

        return $previous;
    }

    public static function restore(): void
    {
        \array_shift(self::$handlers);
    }

    /**
     * @internal
     */
    public static function reset(): void
    {
        self::$handlers = [];
    }

    /**
     * @internal
     */
    public static function fatal(\Throwable $error): void
    {
        $handler = self::$handlers[0] ?? null;

        if (null !== $handler && false !== $handler($error)) {
            return;
        }

        \trigger_error((string) $error, \E_USER_ERROR);
    }
}
