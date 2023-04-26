<?php

namespace bushart\crudmagic;
use Illuminate\Support\ServiceProvider;

class MagicServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Grid', function() {
            return new Grid();
        });

        $this->app->bind('GridDSN', function() {
            return new GridDSN();
        });

        $this->app->bind('CrudHelpers', function() {
            return new CrudHelpers();
        });

        $this->commands([
            magic\Commands\CrudCommand::class,
            magic\Commands\ControllerCommand::class,
            magic\Commands\ServiceCommand::class,
            magic\Commands\ViewCommand::class,
            magic\Commands\RouteCommand::class,
            magic\Commands\ModelCommand::class,
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/magic.php' => config_path('magic.php'),
        ],'config');
        $this->publishes([
            __DIR__ . '/js/magic.js' => public_path('js/magic.js'),
            __DIR__ . '/js/sweet_alert/sweet_alert.min.js' => public_path('js/sweet_alert/sweet_alert.min.js'),
        ],'public');
        require_once __DIR__ . '/magic/Helpers/Grid.php';
        require_once __DIR__ . '/magic/Helpers/GridDSN.php';
        require_once __DIR__ . '/magic/Helpers/CrudHelpers.php';

        $this->loadViewsFrom(__DIR__.'/resources/views', 'crudmagic');

    }
}
