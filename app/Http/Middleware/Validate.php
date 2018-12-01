<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use App\Meta\ValidateRequest;


class Validate
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
         //Validate parameters from request
        $validation = Validator::make($request->all(), ValidateRequest::index());

        if($validation->fails()){
            return response()->json(['message' => $validation->errors()]);
        }

        return $next($request);
    }
}
