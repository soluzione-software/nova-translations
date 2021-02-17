<?php

namespace SoluzioneSoftware\NovaTranslations\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use SoluzioneSoftware\LaravelTranslations\Facades\Translations;
use SoluzioneSoftware\NovaTranslations\Http\Requests\Translations\DeleteRequest;
use SoluzioneSoftware\NovaTranslations\Http\Requests\Translations\StoreRequest;
use SoluzioneSoftware\NovaTranslations\Http\Requests\Translations\UpdateRequest;

class TranslationsController extends Controller
{
    public function get(string $locale)
    {
        $translations = Translations::getTranslations($locale);

        array_walk(
            $translations,
            function (string &$value, string $key) {
                $namespaceKey = explode('::', $key, 2);
                $value = [
                    'namespace' => count($namespaceKey) === 1 ? null : $namespaceKey[0],
                    'key' => Arr::last($namespaceKey),
                    'value' => $value,
                ];
            }
        );

        return Response::json(array_values($translations));
    }

    public function store(string $locale, StoreRequest $request)
    {
        $data = $request->validated();

        Translations::updateTranslation($locale, $data['key'], $data['value'], Arr::get($data, 'namespace'));

        return Response::json([], \Illuminate\Http\Response::HTTP_CREATED);
    }

    public function update(string $locale, UpdateRequest $request)
    {
        $data = $request->validated();

        Translations::updateTranslation($locale, $data['key'], $data['value'], Arr::get($data, 'namespace'));

        return Response::noContent();
    }

    public function delete(DeleteRequest $request)
    {
        $data = $request->validated();

        Translations::removeTranslation($data['locale'], $data['key'], Arr::get($data, 'namespace'));

        return Response::noContent();
    }
}
