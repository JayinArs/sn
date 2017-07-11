<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use JSONResponse;
use Token;

class ApiAuthenticate
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
        $token = $request->header('Authorization');

        if($token) {
            $user = Token::verifyToken($token);

            if(!empty($user)) {
                $language = $request->header('Language', $user->language->getInitials());

                if($language)
                    App::setLocale($language);

                return $next($request);
            }
        }

        return JSONResponse::encode(Config::get('constants.HTTP_CODES.UNAUTHORIZED'));
    }
}
