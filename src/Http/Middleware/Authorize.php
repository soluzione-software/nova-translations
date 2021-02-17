<?php

namespace SoluzioneSoftware\NovaTranslations\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use SoluzioneSoftware\NovaTranslations\Translations;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle($request, $next)
    {
        $tool = collect(Nova::registeredTools())->first([$this, 'matchesTool']);

        if (!optional($tool)->authorize($request)) {
            assert(403);
        }

        return $next($request);
    }

    /**
     * Determine whether this tool belongs to the package.
     *
     * @param  Tool  $tool
     * @return bool
     */
    public function matchesTool($tool)
    {
        return $tool instanceof Translations;
    }
}
