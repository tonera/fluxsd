<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        if( !Session::has('locale' )){
            $accept_lang = $_SERVER['HTTP_ACCEPT_LANGUAGE']??'en';
            $browserLanguage = substr($accept_lang, 0, 2); //read browser language
            
            if ( array_key_exists($browserLanguage, config('languages')) ) {
                Session::put('locale', $browserLanguage);
            } else {
                Session::put('locale', 'en_US');
            }
        }
        // var_dump(Session::has('locale' ),Session::get('locale'));

        //Simply set language from session
        App::setlocale(Session::get('locale'));

        return $next($request);
    }
}
