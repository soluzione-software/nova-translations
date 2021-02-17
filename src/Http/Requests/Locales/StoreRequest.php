<?php

namespace SoluzioneSoftware\NovaTranslations\Http\Requests\Locales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;
use SoluzioneSoftware\LaravelTranslations\Facades\Translations;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'initialize' => [
                'required',
                Rule::in(['true', 'false']),
            ],
            'locale' => [
                'required',
                'alpha_dash',
                'min:2',
                'max:10',
                Rule::notIn(Translations::getLocales()),
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'initialize' => Lang::get('nova-translations::validation.attributes.initialize'),
            'locale' => Lang::get('nova-translations::validation.attributes.locale'),
        ];
    }
}
