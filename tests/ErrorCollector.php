<?php

namespace React\Promise;

final class ErrorCollector
{
    private $errors = [];

    public function start()
    {
        $errors = [];

        set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) use (&$errors) {
            $errors[] = compact('errno', 'errstr', 'errfile', 'errline', 'errcontext');
        });

        $this->errors = &$errors;
    }

    public function stop()
    {
        $errors = $this->errors;
        $this->errors = [];

        restore_error_handler();

        return $errors;
    }
}
