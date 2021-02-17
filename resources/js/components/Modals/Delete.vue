<template>
    <modal @modal-close="handleClose">
        <form
            class="bg-white rounded-lg shadow-lg overflow-hidden"
            style="width: 460px"
            @submit.prevent="handleConfirm"
        >
            <slot/>

            <div class="bg-30 px-6 py-3 flex">
                <div class="ml-auto">
                    <button
                        class="btn text-80 font-normal h-9 px-3 mr-3 btn-link"
                        type="button"
                        @click.prevent="handleClose"
                        :disabled="buttonsDisabled"
                    >
                        {{ __('Cancel') }}
                    </button>

                    <button
                        ref="confirmButton"
                        class="btn btn-default btn-danger text-uppercase"
                        type="submit"
                        :disabled="buttonsDisabled"
                    >
                        {{ __('Delete') }}
                    </button>
                </div>
            </div>
        </form>
    </modal>
</template>

<script>
export default {
    mounted() {
        this.$refs.confirmButton.focus()
    },
    props: {
        buttonsDisabled: {
            type: Boolean,
            default: false,
        },
    },
    methods: {
        handleClose() {
            this.$emit('close')
        },

        handleConfirm() {
            this.$emit('confirm')
        },
    },
}
</script>
