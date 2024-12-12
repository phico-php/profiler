<?php

declare(strict_types=1);

namespace Phico\Profiler;

use BadMethodCallException;
use InvalidArgumentException;
use Phico\Http\{Request, Response};
use Phico\Middleware\MiddlewareInterface;

// adds some default secure headers to the response
class ProfilerMiddleware implements MiddlewareInterface
{
    protected Timer $timer;

    /**
     * The constructor initialises the Timer instance
     * @return void
     */
    public function __construct()
    {
        // @TODO use this when using workerman etc..
        // at the moment timer() is static but and is bound to a single request
        // under workerman it may be bound to all requests
        $this->timer = new Timer();
    }

    /**
     * Handle the Request
     * @param Request $request
     * @param mixed $next
     * @return Response
     * @throws BadMethodCallException
     * @throws InvalidArgumentException
     */
    public function handle(Request $request, $next): Response
    {
        // set timer in request attributes if using workerman
        $request->attrs()->set('timer', $this->timer);

        // add the default phico timer
        if (defined('PHICO_APP_START')) {
            /** @disregard P1011 */
            $this->timer->start('app', 'app start', PHICO_APP_START);
        }
        // continue app
        $response = $next($request);
        // stop the Phico app timer
        if (defined('PHICO_APP_START')) {
            $this->timer->stop('app');
        }
        // add headers to response
        $response->headers->set('X-Peak-Memory', $this->formatMemory(memory_get_peak_usage()));
        $response->headers->set('Server-Timing', $this->formatTimers());

        return $response;
    }

    /**
     * Formats memory usage nicely
     * @param mixed $size
     * @return string
     */
    private function formatMemory($size): string
    {
        $unit = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $unit[$i];
    }

    /**
     * Formats the timer data nicely
     * @return string
     */
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
