<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $currentCompany = null;
                $companies = collect();

                if ($user->current_company_id) {
                    $currentCompany = Company::find($user->current_company_id);
                }

                if ($user->role === 'owner') {
                    $companies = $user->companies;
                }

                $view->with('currentCompany', $currentCompany);
                $view->with('userCompanies', $companies);
            }
        });
    }
}
