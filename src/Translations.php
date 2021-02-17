<?php

namespace SoluzioneSoftware\NovaTranslations;

use Illuminate\Support\Facades\View;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class Translations extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('translations', __DIR__.'/../dist/js/index.js');
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function renderNavigation()
    {
        return View::make('nova-translations::navigation');
    }
}
