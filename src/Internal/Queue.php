<?php

namespace React\Promise\Internal;

/**
 * @internal
 */
final class Queue
{
    private $draining = false;
    private $queue = [];

    public function enqueue(callable $task)
    {
        $this->queue[] = $task;

        if (!$this->draining) {
            $this->drain();
        }
    }

    private function drain()
    {
        $this->draining = true;

        $exception = null;

        while ($task = array_shift($this->queue)) {
            try {
                $task();
            } catch (\Throwable $exception) {
            } catch (\Exception $exception) {
            }
        }

        $this->draining = false;
        $this->queue = [];

        if ($exception) {
            throw $exception;
        }
    }
}
