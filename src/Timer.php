<?php

declare(strict_types=1);

namespace Phico\Profiler;


/**
 * This is the timer class, it tracks timers and their start/stop times
 *
 * @package Profiler
 * @author github.com/indgy
 */
class Timer
{
    /**
     * @var array<string,Timer> - Container for the timers indexed by name
     */
    private $timers = [];


    /**
     * Starts a timer for $name
     *
     * @param string $name The name of the timer as shown in the output header
     * @param string $description An optional description for the timer
     * @param float $time An optional starting timestamp
     * @return Timer
     */
    public function start(string $name, string $description = "", float $time = null): Timer
    {
        $this->timers[$name] = [
            'start' => (is_null($time)) ? microtime(true) : $time,
            'stop' => null,
            'desc' => $description,
        ];
        return $this;
    }
    /**
     * Stops the timer identified by $name by recording the end time
     *
     * @param string $name The name of the timer to stop
     * @param float $time An optional value representing the stop time
     * @return Timer
     */
    public function stop(string $name, float $time = null): Timer
    {
        $this->timers[$name]['stop'] = (is_null($time)) ? microtime(true) : $time;
        return $this;
    }
    /**
     * Returns all the registered timers
     *
     * @return array<string,Timer>
     */
    public function all(): array
    {
        return $this->timers;
    }

}
