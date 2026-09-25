<?php

namespace App\Providers;

use App\Models\Order;
use App\Policies\OrderPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());
        DB::prohibitDestructiveCommands($this->app->isProduction());

        Gate::policy(Order::class, OrderPolicy::class);

        Password::defaults(fn (): Password => $this->app->isProduction()
            ? Password::min(12)->mixedCase()->numbers()->symbols()->uncompromised()
            : Password::min(10)->mixedCase()->numbers());

        RateLimiter::for('checkout', fn (Request $request): Limit => Limit::perMinute(10)
            ->by($request->user()?->getAuthIdentifier() ?: $request->ip()));

        RateLimiter::for('export', fn (Request $request): Limit => Limit::perMinute(5)
            ->by($request->user()?->getAuthIdentifier() ?: $request->ip()));

        if ($this->app->isProduction() && str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
