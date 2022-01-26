<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
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
        if ($roleId === AuthenticateService::AdminRoleId) {
            return $next($request);
        }
        return response()->json(
            ['data' => 'UnAuthenticated'],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
