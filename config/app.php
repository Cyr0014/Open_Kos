<?php

namespace App\Providers;

use App\Contracts\DataTransfer\TabularReader;
use App\Contracts\DataTransfer\TabularWriter;
use App\Models\Setting;
use App\Services\DataTransfer\CsvReader;
use App\Services\DataTransfer\CsvWriter;
use Carbon\CarbonImmutable;
use DateTimeZone;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TabularReader::class, CsvReader::class);
        $this->app->bind(TabularWriter::class, CsvWriter::class);
    }

    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureDisplayTimezone();
        $this->configureAuthEvents();
        $this->configureRateLimiters();
    }

    protected function configureDisplayTimezone(): void
    {
        try {
            $timezone = Setting::get('timezone');
        } catch (QueryException) {
            $timezone = null;
        }

        if (! is_string($timezone) || $timezone === '') {
            $timezone = 'UTC';
        }

        try {
            new DateTimeZone($timezone);
        } catch (\Exception) {
            $timezone = 'UTC';
        }

        config(['app.display_timezone' => $timezone]);
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function configureAuthEvents(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            $event->user->forceFill(['last_login_at' => now()])->save();
        });
    }

    protected function configureRateLimiters(): void
    {
        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(10)
            ->by($request->ip() ?: 'unknown'));

        RateLimiter::for('payment-webhook', fn (Request $request) => Limit::perMinute(20)
            ->by($request->ip() ?: 'unknown'));

        RateLimiter::for('public-portal', fn (Request $request) => Limit::perMinute(60)
            ->by($request->ip() ?: 'unknown'));
    }
}
