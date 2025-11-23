<?php

namespace LivewireFilemanager\Filemanager\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FilemanagerAccess
{
    public function handle(Request $request, Closure $next)
    {
        $callback = config('livewire-filemanager.callbacks.access_check');

        if ($callback && is_callable($callback)) {
            $result = call_user_func($callback, $request);

            if ($result === false) {
                return response()->json(['message' => 'Access denied'], Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}
