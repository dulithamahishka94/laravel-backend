<?php

namespace App\Providers;

use App\Http\Controllers\ApproveController;
use App\Models\Forum;
use App\Models\User;
use App\Policies\ForumPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        ApproveController::class => ForumPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }

        Gate::define('approve-forum', [ForumPolicy::class, 'approveForum']);
        Gate::define('reject-forum', [ForumPolicy::class, 'rejectForum']);
        Gate::define('post-without-approval', [ForumPolicy::class, 'postWithoutApproval']);
    }
}
