<?php

namespace SoluzioneSoftware\NovaTranslations\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use SoluzioneSoftware\LaravelTranslations\Facades\Translations;
use SoluzioneSoftware\NovaTranslations\Http\Requests\Translations\DeleteRequest;
use SoluzioneSoftware\NovaTranslations\Http\Requests\Translations\StoreRequest;
use SoluzioneSoftware\NovaTranslations\Http\Requests\Translations\UpdateRequest;

class TranslationsController extends Controller
{
    public function get(Request $request, string $locale)
    {
        $search = strtolower((string) $request->input('search'));

        $translations = Translations::getTranslations($locale)
            ->filter(function (array $translation) use ($search) {
                return empty($search)
                    || str_contains(strtolower($translation['namespace']), $search)
                    || str_contains(strtolower($translation['key']), $search)
                    || str_contains(strtolower($translation['value']), $search);
            })
            ->sortBy(function (array $item) {
                return $item['namespace'].'_'.$item['key'];
            });

        return Response::json(array_values($translations->all()));
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
