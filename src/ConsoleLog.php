<?php

include_once 'Logger.php';

/**
 * A thin Console Logger.
 *
 * User: Rafael da Silva Ferreira
 * Date: 5/13/17
 * Time: 20:17
 */
final class ConsoleLog implements Logger
{
    private const RED = "\033[31m%s \n";
    private const GREEN = "\033[32m%s \n";
    private const YELLOW = "\033[33m%s \n";

    function error(string $message)
    {
        echo sprintf(self::RED, $message);
    }

    function warning(string $message)
    {
        echo sprintf(self::YELLOW, $message);
    }

    function success(string $message)
    {
        echo sprintf(self::GREEN, $message);
    }
}