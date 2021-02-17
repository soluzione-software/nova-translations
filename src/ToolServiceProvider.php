<?php

namespace SoluzioneSoftware\NovaTranslations;

use Illuminate\Container\Container;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use SoluzioneSoftware\NovaTranslations\Http\Middleware\Authorize;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'nova-translations');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'nova-translations');

        $this->publishes([
            __DIR__.'/../resources/lang' => 'resources/lang/vendor/nova-translations',
        ], ['nova-translations', 'lang', 'nova-translations-lang']);

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            /** @var Loader $loader */
            $loader = Container::getInstance()->make('translation.loader');
            Nova::translations(
                array_merge(
                    Arr::dot($loader->load('en', 'nova', 'nova-translations'), 'nova-translations::'),
                    Arr::dot($loader->load(Config::get('app.fallback_locale'), 'nova', 'nova-translations'),
                        'nova-translations::'),
                    Arr::dot($loader->load(App::getLocale(), 'nova', 'nova-translations'), 'nova-translations::')
                )
            );
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if (App::routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
            ->namespace('SoluzioneSoftware\NovaTranslations\Http\Controllers')
            ->prefix('nova-vendor/translations')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
