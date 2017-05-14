<?php

/**
 * Interface Logger.
 */
interface Logger {
    function error(string $message);
    function warning(string $message);
    function success(string $message);
}