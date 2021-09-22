<?php

namespace SoluzioneSoftware\NovaTranslations\Http\Controllers;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Laravel\Nova\Resource;
use SoluzioneSoftware\LaravelTranslations\Facades\Translations;
use SoluzioneSoftware\NovaTranslations\Http\Requests\Translations\DeleteRequest;
use SoluzioneSoftware\NovaTranslations\Http\Requests\Translations\StoreRequest;
use SoluzioneSoftware\NovaTranslations\Http\Requests\Translations\UpdateRequest;

class TranslationsController extends Controller
{
    /**
     * @param  Request  $request
     * @param  string  $locale
     * @return JsonResponse
     * @throws BindingResolutionException
     */
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
                return Arr::get($item, 'source', '').$item['namespace'].$item['key'];
            });

        $total = $translations->count();
        $perPage = Resource::perPageOptions()[0];
        $page = Paginator::resolveCurrentPage();
        $items = $translations->forPage($page, $perPage)->values();

        $paginator = Container::getInstance()
            ->makeWith(
                LengthAwarePaginator::class,
                compact('items', 'total', 'perPage', 'page')
            );

        return Response::json($paginator);
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

        Translations::updateTranslation(
            $locale,
            $data['key'],
            (string) Arr::get($data, 'value'),
            Arr::get($data, 'namespace')
        );

        return Response::noContent();
    }

    public function delete(DeleteRequest $request)
    {
        $data = $request->validated();

        Translations::removeTranslation($data['locale'], $data['key'], Arr::get($data, 'namespace'));

        return Response::noContent();
    }
}
