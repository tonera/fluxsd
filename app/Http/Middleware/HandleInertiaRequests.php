<?php

namespace App\Http\Middleware;

use App\Services\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;
use Illuminate\Support\Facades\Session;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $cacheKey = config('public.partjob_status_key');
        return array_merge(parent::share($request), [
            'tuseApi' => env('TUSE_API'),
            'tuseHasRegister' => !empty(Common::getConfigKeyValue('engine.atz.token')),
            'locale' => Session::get('locale')??'en_US',
            'languages' => config('languages'),
            'oddStatus' => Cache::get($cacheKey) == 'online',
            'reverbHost' => env('REVERB_HOST')
        ]);
    }
}
