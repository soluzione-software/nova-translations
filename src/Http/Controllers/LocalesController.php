<?php

namespace SoluzioneSoftware\NovaTranslations\Http\Controllers;

use Illuminate\Support\Facades\Response;
use SoluzioneSoftware\LaravelTranslations\Facades\Translations;
use SoluzioneSoftware\NovaTranslations\Http\Requests\Locales\StoreRequest;

class LocalesController extends Controller
{
    public function get()
    {
        return Response::json(Translations::getLocales());
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
}
