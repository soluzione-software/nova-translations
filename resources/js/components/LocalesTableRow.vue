<template>
    <TableRow>
        <td>{{ item }}</td>
        <td class="td-fit text-right pr-6 align-middle">
            <!-- View Link -->
            <span class="inline-flex">
                <router-link
                    class="cursor-pointer text-70 hover:text-primary mr-3 inline-flex items-center"
                    v-tooltip.click="__('View')"
                    :to="{
                        name: 'translations.locales.show',
                        params: {
                            locale: item,
                        },
                    }"
                >
                    <icon type="view" width="20" height="20" view-box="-1 -2 23 20"/>
                </router-link>
            </span>

            <!-- Sync Link -->
            <span class="inline-flex">
                <button
                    v-tooltip.click="__('nova-translations::synchronize')"
                    class="text-70 hover:text-primary mr-3"
                    :disabled="isWorking"
                    @click.prevent="sync"
                >
                    <svg fill="none" stroke="currentColor" width="20" height="20" viewBox="2 2 20 20"
                         xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </button>
            </span>

            <!-- Import Link -->
            <span class="inline-flex">
                <button
                    v-tooltip.click="__('nova-translations::import')"
                    class="text-70 hover:text-primary mr-3"
                    @click.prevent="openImportModal"
                >
                    <svg fill="none" stroke="currentColor" width="20" height="20" viewBox="2 2 20 20"
                         xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                </button>
            </span>

            <!-- Export Link -->
            <span class="inline-flex">
                <a
                    :href="`/nova-vendor/translations/locales/${this.item}/export`"
                    v-tooltip.click="__('nova-translations::export')"
                    class="cursor-pointer text-70 hover:text-primary mr-3 inline-flex items-center"
                >
                    <svg fill="none" stroke="currentColor" width="20" height="20" viewBox="2 2 20 20"
                         xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </a>
            </span>

            <!-- Delete Link -->
            <button
                v-tooltip.click="__('Delete')"
                class="inline-flex appearance-none cursor-pointer text-70 hover:text-primary"
                @click.prevent="openDeleteModal"
            >
                <icon/>
            </button>

            <portal
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
                        <heading :level="2" class="mb-6">
                            {{ __('nova-translations::delete_locale_modal_title') }}
                        </heading>
                        <p class="text-80 leading-normal">
                            {{ __('nova-translations::delete_locale_modal_text') }}
                        </p>
                    </div>
                </DeleteModal>

                <modal
                    v-if="importModalOpen"
                    ref="importModal"
                    @modal-close="closeImportModal"
                >
                    <form
                        class="bg-white rounded-lg shadow-lg overflow-hidden"
                        style="width: 600px"
                        @submit.prevent="handleConfirmImportModal"
                    >
                        <div class="p-8">
                            <form-file-field
                                ref="importFileField"
                                :field="importFileField"
                                :errors="validationErrors"
                            />
                        </div>

                        <div class="bg-30 px-6 py-3 flex">
                            <div class="ml-auto">
                                <button
                                    class="btn text-80 font-normal h-9 px-3 mr-3 btn-link"
                                    type="button"
                                    :disabled="isWorking"
                                    @click.prevent="closeImportModal"
                                >
                                    {{ __('Cancel') }}
                                </button>

                                <button
                                    ref="confirmButton"
                                    class="btn btn-default btn-primary text-uppercase"
                                    type="submit"
                                    :disabled="isWorking"
                                >
                                    {{ __('nova-translations::upload') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </modal>
            </portal>
        </td>
    </TableRow>
</template>

<script>
import TableRow from './TableRow';
import DeleteModal from '../components/Modals/Delete';
import {Errors} from "form-backend-validation";

export default {
    components: {
        TableRow,
        DeleteModal,
    },
    props: {
        item: {
            required: true,
        },
    },
    data() {
        return {
            deleteModalOpen: false,
            isWorking: false,
            importModalOpen: false,
            importFileField: {
                name: this.__('nova-translations::file'),
                validationKey: 'file',
                required: true,
                acceptedTypes: 'text/csv',
            },
            validationErrors: new Errors(),
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
                .delete(`/nova-vendor/translations/locales/${this.item}`)
                .then(() => {
                    this.deleteModalOpen = false
                    this.$emit('deleted', this.item);
                })
                .finally(() => {
                    this.isWorking = false;
                });
        },

        async sync() {
            this.isWorking = true;

            try {
                await this.createSyncRequest()

                Nova.success(this.__('nova-translations::locale_synchronized'))

                this.importModalOpen = false;
                this.validationErrors = new Errors();
            } catch (error) {
                Nova.error(
                        this.__('There was a problem submitting the form.') +
                        ' "' +
                        error.response.statusText +
                        '"'
                    )
            } finally {
                this.isWorking = false;
            }
        },

        openImportModal() {
            this.importModalOpen = true;
        },

        closeImportModal() {
            if (!this.isWorking) {
                this.importModalOpen = false;
            }
        },

        async handleConfirmImportModal() {
            this.isWorking = true;

            try {
                await this.createRequest()

                Nova.success(this.__('nova-translations::locale_imported'))

                this.importModalOpen = false;
                this.validationErrors = new Errors();
            } catch (error) {
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
                this.isWorking = false;
            }
        },

        createRequest() {
            let formData = new FormData();

            if (this.$refs.importFileField.file) {
                formData.set('file', this.$refs.importFileField.file);
            }

            return Nova.request()
                .post(`/nova-vendor/translations/locales/${this.item}/import`, formData);
        },

        createSyncRequest() {
            return Nova.request()
                .post(`/nova-vendor/translations/locales/${this.item}/sync`);
        },
    }
}
</script>
