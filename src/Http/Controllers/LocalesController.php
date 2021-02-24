<?php

namespace SoluzioneSoftware\NovaTranslations\Http\Controllers;

use Illuminate\Support\Facades\Response;
use SoluzioneSoftware\LaravelTranslations\Facades\Translations;
use SoluzioneSoftware\NovaTranslations\Http\Requests\Locales\ImportRequest;
use SoluzioneSoftware\NovaTranslations\Http\Requests\Locales\StoreRequest;

class LocalesController extends Controller
{
    public function get()
    {
        return Response::json(Translations::getLocales()->sort());
    }

    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        Translations::addLocale($data['locale'], $data['initialize'] === 'true');

        return Response::json([], \Illuminate\Http\Response::HTTP_CREATED);
    }

    public function delete(string $locale)
    {
        Translations::removeLocale($locale);

        return Response::noContent();
    }

    public function import(ImportRequest $request, string $locale)
    {
        $request->validated();

        Translations::import($locale, $request->file('file')->getPathname());

        return Response::noContent();
    }

    public function export(string $locale)
    {
        $file = tempnam(sys_get_temp_dir(), '');

        Translations::export($locale, $file);

        return Response::download($file, "$locale.csv");
    }

    public function sync(string $locale)
    {
        Translations::sync($locale);

        return Response::noContent();
    }
}
