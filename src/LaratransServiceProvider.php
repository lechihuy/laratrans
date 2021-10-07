<?php

namespace Laratrans;

use Illuminate\Support\ServiceProvider;

class LaratransServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerPublishing();
    }

    /**
     * Register the package publishings.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/translatable.php', 'translatable'
        );

        $this->registerHelper();
    }

    /**
     * Register the commands of the package.
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        if (! $this->app->runningInConsole()) return;

        $this->commands([
            \Laratrans\Console\InstallCommand::class,
        ]);
    }

    /**
     * Register the publishing of the package.
     *
     * @return void
     */
    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/translatable.php' => base_path('config/translatable.php'),
        ], 'laratrans-config');
    }

    /**
     * Register the helper of package.
     *
     * @return void
     */
    protected function registerHelper(): void
    {
        $this->app->singleton('locale', Locale::class);
    }
}
