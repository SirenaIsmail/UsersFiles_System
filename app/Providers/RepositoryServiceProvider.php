<?php

namespace App\Providers;

use App\Repositories\group\GroupRepository;
use App\Repositories\file\IFileRepository;
use App\Repositories\file\FileRepository;
use App\Repositories\group\IGroupRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IFileRepository::class,FileRepository::class);
        $this->app->bind(IGroupRepository::class,GroupRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
