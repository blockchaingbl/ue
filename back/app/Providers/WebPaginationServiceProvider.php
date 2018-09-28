<?php

namespace App\Providers;


use App\Library\FanwePresenter;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Pagination\Paginator;

class WebPaginationServiceProvider extends PaginationServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Paginator::presenter(function($paginator){
            return new FanwePresenter($paginator);
        });
    }
}
