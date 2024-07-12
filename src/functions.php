<?php

function timer(): \Phico\Profiler\Timer
{
    static $timer;
    $timer = ($timer) ? $timer : new \Phico\Profiler\Timer();
    return $timer;

}
