<template>
    <div class="flex items-center">
        <template v-if="editing">
            <textarea
                ref="textarea"
                v-model="item.value"
                rows="1"
                class="w-full form-control form-input form-input-bordered py-1 h-auto"
                :disabled="saving"
                @blur="onSave"
            ></textarea>
            <div class="ml-4">
                <svg
                    v-if="saving"
                    xmlns="http://www.w3.org/2000/svg"
                    class="text-warning"
                    fill="none"
                    stroke="currentColor"
                    width="20"
                    height="20"
                    viewBox="2 2 20 20"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <button
                    v-else
                    v-tooltip.click="__('nova-translations::save')"
                    class="inline-flex appearance-none text-70 hover:text-primary"
                    @click.prevent="onSave"
                >
                    <icon type="check-circle" view-box="2 2 20 20"/>
                </button>
            </div>
        </template>
        <template v-else>
            <div class="w-full" @click="onEdit">{{ item.value }}</div>
            <button
                v-tooltip.click="__('Edit')"
                class="inline-flex appearance-none cursor-pointer text-70 hover:text-primary ml-4"
                @click.prevent="onEdit"
            >
                <icon type="edit"/>
            </button>
        </template>
    </div>
</template>

<script>

export default {
    props: {
        locale: {
            type: String,
            required: true,
        },
        item: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            editing: false,
            saving: false,
        }
    },
    methods: {
        onEdit() {
            this.editing = true;
            this.$nextTick(() => {
                this.$refs.textarea.focus();
            });
        },
        async onSave() {
            this.saving = true;

            try {
                await this.createRequest()

                Nova.success(this.__('nova-translations::translation_updated'))

                this.editing = false;
            } catch (error) {
                Nova.error(
                    this.__('There was a problem submitting the form.') +
                    ' "' +
                    error.response.statusText +
                    '"'
                )
            } finally {
                this.saving = false
            }

            setTimeout(() => {
                this.editing = false;
                this.saving = false;
            }, 1000)
        },

        createRequest() {
            return Nova
                .request()
                .put(`/nova-vendor/translations/translations/${this.locale}`, this.item);
        },
    },
}
</script>
