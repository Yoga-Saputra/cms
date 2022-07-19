<?php

namespace App\Http\Middleware;

use App\Category;
use Closure;

class VerifyCategoriesCount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Category::all()->count() === 0) {
            return response()->json([
                'status' => false,
                'message' => 'You need create categories to be able create a post'
            ]);
        }
        return $next($request);
    }
}
