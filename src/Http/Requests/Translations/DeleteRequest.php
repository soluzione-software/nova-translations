<?php

namespace SoluzioneSoftware\NovaTranslations\Http\Requests\Translations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class DeleteRequest extends FormRequest
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
            'locale' => [
                'required',
                'alpha_dash',
            ],
            'namespace' => [
                'nullable',
                'alpha_dash',
                'max:100',
            ],
            'key' => [
                'required',
                'string',
                'max:300',
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
            'key' => Lang::get('nova-translations::validation.attributes.key'),
            'namespace' => Lang::get('nova-translations::validation.attributes.namespace'),
            'value' => Lang::get('nova-translations::validation.attributes.value'),
        ];
    }
}
