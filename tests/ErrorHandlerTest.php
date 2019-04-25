<?php

namespace React\Promise;

use React\Promise\PromiseAdapter\CallbackPromiseAdapter;

class ErrorHandlerTest extends TestCase
{
    /** @test */
    public function shouldUseCustomHandler(): void
    {
        ErrorHandler::set(static function($err) use (&$error) {
            $error = $err;
        });

        $expected = new \Exception('Test');
        ErrorHandler::fatal($expected);

        self::assertSame($expected, $error);

        ErrorHandler::reset();
    }

    /** @test */
    public function shouldReturnPreviousHandlerOnSet(): void
    {
        ErrorHandler::set($f = static function() {});

        self::assertSame($f, ErrorHandler::set(null));

        ErrorHandler::reset();
    }

    /** @test */
    public function shouldUseDefaultForNullHandler(): void
    {
        ErrorHandler::set(static function($err) use (&$error) {
            $error = $err;
        });
        ErrorHandler::set(null);

        $errorCollector = new ErrorCollector();
        $errorCollector->start();

        $expected = new \Exception('Test');
        ErrorHandler::fatal($expected);

        $errors = $errorCollector->stop();

        self::assertStringContainsString('Exception: Test', $errors[0]['errstr']);
        self::assertNull($error);

        ErrorHandler::reset();
    }

    /** @test */
    public function shouldRestoreHandler(): void
    {
        ErrorHandler::set(static function($err) use (&$error) {
            $error = $err;
        });
        ErrorHandler::restore();

        $errorCollector = new ErrorCollector();
        $errorCollector->start();

        $expected = new \Exception('Test');
        ErrorHandler::fatal($expected);

        $errors = $errorCollector->stop();

        self::assertStringContainsString('Exception: Test', $errors[0]['errstr']);
        self::assertNull($error);

        ErrorHandler::reset();
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function shouldAllowRestoringMultipleTimesWithoutHandlers(): void
    {
        ErrorHandler::reset();

        ErrorHandler::restore();
        ErrorHandler::restore();
        ErrorHandler::restore();
        ErrorHandler::restore();
    }
}
