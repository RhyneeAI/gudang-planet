<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            return $isLocal ||
                   $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag();
        });

        // Setup gate dan auth untuk Telescope
        $this->authorization();
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user = null) {
            // Cek token dari session
            $token = session('telescope_token');

            if (!$token) {
                return false;
            }

            // Verifikasi token via Sanctum
            $accessToken = PersonalAccessToken::findToken($token);

            if (!$accessToken || !$accessToken->tokenable) {
                return false;
            }

            $tokenUser = $accessToken->tokenable;

            // Hanya superadmin
            return in_array($tokenUser->role->value, [
                Role::SUPERADMIN->value,
            ]);
        });
    }

    protected function authorization()
    {
        $this->gate();

        // Middleware TelescopeAuth akan handle authorization
        // Jadi tidak perlu Telescope::auth() callback
        // Hanya keep gate definition untuk reference
    }
}
