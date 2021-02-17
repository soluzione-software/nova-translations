<template>
    <div>
        <heading class="mb-6">{{ __('nova-translations::create_locale') }}</heading>

        <form
            ref="form"
            autocomplete="off"
            @submit.prevent="onSubmit"
        >
            <card class="mb-8">
                <form-boolean-field
                    ref="initializeField"
                    :field="initializeField"
                    :errors="validationErrors"
                    show-help-text
                />
                <form-text-field
                    ref="localeField"
                    :field="localeField"
                    :errors="validationErrors"
                />
            </card>

            <div class="flex items-center">
                <cancel-button :disabled="isWorking" @click="$router.push({name: 'translations.index'})"/>
                <progress-button :disabled="isWorking" type="submit">{{ __('nova-translations::create_locale') }}</progress-button>
            </div>
        </form>
    </div>
</template>

<script>
import {Localization} from '../mixins';
import {Errors} from 'form-backend-validation';

export default {
    mixins: [Localization],
    data() {
        return {
            initializeField: {
                name: this.__('nova-translations::initialize'),
                attribute: 'initialize',
                validationKey: 'initialize',
                helpText: this.__('nova-translations::initialized_help'),
            },
            localeField: {
                name: this.__('nova-translations::locale'),
                attribute: 'locale',
                validationKey: 'locale',
                required: true,
                extraAttributes: {
                    required: true,
                    minlength: 2,
                    maxlength: 10,
                },
            },
            validationErrors: new Errors(),
            isWorking: false,
        }
    },
    methods: {
        async onSubmit() {
            this.isWorking = true;

            if (this.$refs.form.reportValidity()) {
                try {
                    await this.createRequest()

                    Nova.success(this.__('nova-translations::locale_created'))

                    this.$router.push({name: 'translations.index'})
                } catch (error) {
                    window.scrollTo(0, 0)

                    if (error.response.status === 422) {
                        this.validationErrors = new Errors(error.response.data.errors)
                        Nova.error(this.__('There was a problem submitting the form.'))
                    } else {
                        Nova.error(
                            this.__('There was a problem submitting the form.') +
                            ' "' +
                            error.response.statusText +
                            '"'
                        )
                    }
                } finally {
                    this.isWorking = false
                }
            }
        },

        createRequest() {
            let formData = new FormData();
            this.$refs.initializeField.fill(formData);
            this.$refs.localeField.fill(formData);

            return Nova
                .request()
                .post('/nova-vendor/translations/locales', formData);
        },
    },
}
</script>
