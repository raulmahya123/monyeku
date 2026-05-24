<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();
        $companyId = $user->current_company_id;

        if (!$companyId) {
            abort(403, 'No active company.');
        }

        $pivotRole = $user->companies()->where('company_id', $companyId)->first()?->pivot->role;

        if (!$pivotRole || !in_array($pivotRole, $roles)) {
            abort(403, 'Unauthorized role.');
        }

        return $next($request);
    }
}
