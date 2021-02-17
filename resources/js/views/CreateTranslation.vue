<template>
    <form
        ref="form"
        @submit.prevent="onSubmit"
        autocomplete="off"
    >
        <card class="mb-8">
            <form-text-field
                :errors="validationErrors"
                :field="namespaceField"
                show-help-text
            />
            <form-text-field
                :errors="validationErrors"
                :field="keyField"
                show-help-text
            />
            <form-textarea-field
                :errors="validationErrors"
                :field="valueField"
            />
        </card>

        <div class="flex items-center">
            <cancel-button :disabled="isWorking"
                           @click="$router.push({name: 'translations.locales.show', params: {locale: $route.params.locale}})"/>
            <progress-button :disabled="isWorking" type="submit">{{ __('nova-translations::create_translation') }}</progress-button>
        </div>
    </form>
</template>

<script>
import {Localization} from '../mixins';
import {Errors} from 'form-backend-validation';

export default {
    mixins: [Localization],
    data() {
        return {
            value: null,
            namespaceField: {
                name: this.__('nova-translations::namespace'),
                attribute: 'namespace',
                validationKey: 'namespace',
                helpText: this.__('nova-translations::example_help', {example: 'nova'}),
                extraAttributes: {
                    name: 'namespace',
                    maxlength: 100,
                },
            },
            keyField: {
                name: this.__('nova-translations::key'),
                attribute: 'key',
                validationKey: 'key',
                required: true,
                helpText: this.__('nova-translations::example_help', {example: 'validation.attached'}),
                extraAttributes: {
                    name: 'key',
                    required: true,
                    maxlength: 300,
                },
            },
            valueField: {
                name: this.__('nova-translations::value'),
                attribute: 'value',
                validationKey: 'value',
                required: true,
                extraAttributes: {
                    name: 'value',
                    required: true,
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

                    Nova.success(this.__('nova-translations::translation_created'))

                    this.$router.push({name: 'translations.locales.show', params: {locale: this.$route.params.locale}})
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
            return Nova
                .request()
                .post(`/nova-vendor/translations/translations/${this.$route.params.locale}`, new FormData(this.$refs.form))
        },
    },
}
</script>
