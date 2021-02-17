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
                        <icon type="view" width="22" height="18" view-box="0 0 22 16"/>
                      </router-link>
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
                        <heading :level="2" class="mb-6">{{ __('nova-translations::delete_locale_modal_title') }}</heading>
                        <p class="text-80 leading-normal">
                            {{ __('nova-translations::delete_locale_modal_text') }}
                        </p>
                    </div>
                </DeleteModal>
            </portal>
        </td>
    </TableRow>
</template>

<script>
import TableRow from './TableRow';
import DeleteModal from '../components/Modals/Delete';

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
    }
}
</script>
