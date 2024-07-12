<?php

declare(strict_types=1);

namespace Phico\Profiler;

use Phico\Http\Request;
use Phico\Middleware\MiddlewareInterface;


// adds some default secure headers to the response
class ProfilerMiddleware implements MiddlewareInterface
{
    protected Timer $timer;


    // @TODO use this when using workerman etc..
    // at the moment timer() is static but and is bound to a single request
    // under workerman it may be bound to all requests
    // public function __construct()
    // {
    //     $this->timer = new Timer;
    // }
    function handle(Request $request, $next)
    {
        // set timer in request attributes if using workerman
        // $request->attr->set('timer', $this->timer);

        // add the default phico timer
        if (defined('PHICO_APP_START')) {
            timer()->start('app', 'app start', PHICO_APP_START);
        }
        // continue app
        $response = $next($request);
        // stop the Phico app timer
        if (defined('PHICO_APP_START')) {
            timer()->stop('app');
        }
        // add headers to response
        $response->headers->set('X-Peak-Memory', $this->formatMemory(memory_get_peak_usage()));
        $response->headers->set('Server-Timing', $this->formatTimers());

        return $response;
    }

    private function formatMemory($size): string
    {
        $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $unit[$i];
    }
    private function formatTimers(): string
    {
        $parts = [];

        foreach (timer()->all() as $name => $timer) {
            $out = (null === $timer['stop'])
                ? sprintf('%s;dur=NS', $name)
                : sprintf('%s;dur=%.1f', $name, ($timer['stop'] - $timer['start']) * 1000);

            $out .= (null === $timer['desc'])
                ? sprintf(';desc="%s"', addslashes($timer['desc']))
                : null;

            $parts[] = $out;
        }

        return implode(', ', $parts);
    }
}
