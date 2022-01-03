<?php

namespace App\Http\Middleware;

use App\Services\HttpResponse;
use Closure;
use Illuminate\Http\Request;
use App\Services\AuthenticateService;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $roleId = AuthenticateService::getUserRole($request);
        if ($roleId == 2) {
            return $next($request);
        }
        return response()->json(
            ['data' => 'UnAuthenticated'],
            HttpResponse::HTTP_UNAUTHORIZED
        );
    }
}
