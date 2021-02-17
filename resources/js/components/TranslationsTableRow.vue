<template>
    <TableRow>
        <td>{{ item.namespace || '&mdash;' }}</td>
        <td>{{ item.key }}</td>
        <td style="min-width: 50%">
            <TranslationInput :locale="locale" :item="item"/>
        </td>

        <td class="td-fit text-right pr-6 align-middle">
            <!-- Delete Link -->
            <button
                v-tooltip.click="__('Delete')"
                class="inline-flex appearance-none cursor-pointer text-70 hover:text-primary"
                @click.prevent="openDeleteModal"
            >
                <icon/>
            </button>

            <portal
                v-if="deleteModalOpen"
                to="modals"
                transition="fade-transition"
            >
                <DeleteModal
                    v-if="deleteModalOpen"
                    :buttons-disabled="isWorking"
                    @close="closeDeleteModal"
                    @confirm="confirmDelete"
                >
                    <div class="p-8">
                        <heading :level="2" class="mb-6">{{ __('nova-translations::delete_translation_modal_title') }}</heading>
                        <p class="text-80 leading-normal">
                            {{ __('nova-translations::delete_translation_modal_text') }}
                        </p>
                    </div>
                </DeleteModal>
            </portal>
        </td>
    </TableRow>
</template>

<script>
import DeleteModal from '../components/Modals/Delete';
import TableRow from './TableRow';
import TranslationInput from './TranslationInput';

export default {
    components: {
        DeleteModal,
        TableRow,
        TranslationInput,
    },
    props: {
        locale: {
            required: true,
            type: String,
        },
        item: {
            required: true,
        },
    },
    data() {
        return {
            deleteModalOpen: false,
            isWorking: false,
        }
    },
    methods: {
        openDeleteModal() {
            this.deleteModalOpen = true
        },

        confirmDelete() {
            this.deleteLocale();
            this.closeDeleteModal()
        },

        closeDeleteModal() {
            if (!this.isWorking) {
                this.deleteModalOpen = false
            }
        },

        deleteLocale() {
            this.isWorking = true;

            Nova.request()
                .delete(
                    '/nova-vendor/translations/translations',
                    {
                        params: {
                            locale: this.locale,
                            ...this.item,
                        }
                    }
                )
                .then(() => {
                    this.deleteModalOpen = false
                    this.$emit('deleted', this.item);
                })
                .finally(() => {
                    this.isWorking = false;
                });
        },
    }
}
</script>
