<template>
    <div>
        <heading class="mb-3">{{ __('nova-translations::translations') }}</heading>

        <div class="flex mb-6">
            <!-- Search -->
            <div class="relative h-9 flex-no-shrink">
                <icon type="search" class="absolute search-icon-center ml-3 text-70"/>

                <input
                    class="appearance-none form-search w-search pl-search shadow"
                    :placeholder="__('Search')"
                    type="search"
                    v-model.trim="search"
                    @keydown.stop="performSearch"
                    spellcheck="false"
                />
            </div>

            <div class="w-full text-right">
                <router-link
                    :to="{name: 'translations.translations.create', params: {locale: $route.params.locale}}"
                    class="btn btn-default btn-primary"
                >
                    {{ __('nova-translations::create_translation') }}
                </router-link>
            </div>
        </div>
        <card>
            <loading-view :loading="loading">
                <div
                    v-if="!translations.length"
                    class="flex justify-center items-center px-6 py-8"
                >
                    <div class="text-center">
                        <svg
                            class="mb-3"
                            height="51"
                            viewBox="0 0 65 51"
                            width="65"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M56 40h2c.552285 0 1 .447715 1 1s-.447715 1-1 1h-2v2c0 .552285-.447715 1-1 1s-1-.447715-1-1v-2h-2c-.552285 0-1-.447715-1-1s.447715-1 1-1h2v-2c0-.552285.447715-1 1-1s1 .447715 1 1v2zm-5.364125-8H38v8h7.049375c.350333-3.528515 2.534789-6.517471 5.5865-8zm-5.5865 10H6c-3.313708 0-6-2.686292-6-6V6c0-3.313708 2.686292-6 6-6h44c3.313708 0 6 2.686292 6 6v25.049375C61.053323 31.5511 65 35.814652 65 41c0 5.522847-4.477153 10-10 10-5.185348 0-9.4489-3.946677-9.950625-9zM20 30h16v-8H20v8zm0 2v8h16v-8H20zm34-2v-8H38v8h16zM2 30h16v-8H2v8zm0 2v4c0 2.209139 1.790861 4 4 4h12v-8H2zm18-12h16v-8H20v8zm34 0v-8H38v8h16zM2 20h16v-8H2v8zm52-10V6c0-2.209139-1.790861-4-4-4H6C3.790861 2 2 3.790861 2 6v4h52zm1 39c4.418278 0 8-3.581722 8-8s-3.581722-8-8-8-8 3.581722-8 8 3.581722 8 8 8z"
                                fill="#A8B9C5"
                            />
                        </svg>

                        <h3 class="text-base text-80 font-normal">
                            {{ __('nova-translations::empty') }}
                        </h3>
                    </div>
                </div>

                <template v-else>
                    <Table :items="translations">
                        <template #head>
                            <th class="text-left">
                                <span>{{ __('nova-translations::namespace') }}</span>
                            </th>
                            <th class="text-left">
                                <span>{{ __('nova-translations::key') }}</span>
                            </th>
                            <th class="text-left">
                                <span>{{ __('nova-translations::value') }}</span>
                            </th>
                        </template>

                        <TranslationsTableRow
                            slot="row"
                            slot-scope="{ item }"
                            :locale="locale"
                            :item="item"
                            @deleted="loadTranslations"
                        />
                    </Table>

                    <!-- Pagination -->
                    <pagination-simple
                        :next="hasNextPage"
                        :previous="hasPreviousPage"
                        :pages="totalPages"
                        :page="currentPage"
                        :per-page="perPage"
                        :resource-count-label="countLabel"
                        :current-resource-count="translations.length"
                        :all-matching-resource-count="allMatchingCount"
                        @page="selectPage"
                    >
                        <span class="text-sm text-80 px-4">{{ countLabel }}</span>
                    </pagination-simple>
                </template>
            </loading-view>
        </card>
    </div>
</template>

<script>
import Table from '../components/Table';
import {InteractsWithQueryString, Minimum, Paginatable, PerPageable} from "laravel-nova";
import TranslationsTableRow from "../components/TranslationsTableRow";

export default {
    mixins: [
        InteractsWithQueryString,
        Paginatable,
        PerPageable,
    ],
    components: {
        TranslationsTableRow,
        Table,
    },
    data() {
        return {
            debouncer: null,
            loading: true,
            search: '',
            response: null,
            allMatchingCount: 0,
        }
    },
    watch: {
        currentPage() {
            this.loadTranslations()
        },
    },
    created() {
        this.debouncer = _.debounce(callback => callback(), 500)
    },
    mounted() {
        this.loadTranslations();
    },
    methods: {
        loadTranslations() {
            this.loading = true

            this.$nextTick(() => {
                return Minimum(
                    Nova.request().get(`/nova-vendor/translations/translations/${this.locale}`, {params: this.requestQueryString}),
                    300
                )
                    .then(({data}) => {
                        this.response = data
                        this.perPage = data.per_page
                        this.allMatchingCount = data.total
                        this.loading = false
                    })
            })
        },

        performSearch(event) {
            if (this.loading) {
                return;
            }

            this.debouncer(() => {
                // Only search if we're not tabbing into the field
                if (event.which !== 9) {
                    this.selectPage(1);
                    this.loadTranslations();
                }
            })
        },

        selectPage(page) {
            this.updateQueryString({[this.pageParameter]: page})
        },
    },
    computed: {
        translations() {
            return this.response ? this.response.data : [];
        },

        locale() {
            return this.$route.params.locale;
        },

        hasNextPage() {
            return Boolean(
                this.response && this.response.next_page_url
            )
        },

        hasPreviousPage() {
            return Boolean(
                this.response && this.response.prev_page_url
            )
        },

        totalPages() {
            return Math.ceil(this.allMatchingCount / this.perPage)
        },

        countLabel() {
            const first = this.perPage * (this.currentPage - 1)

            return (
                this.translations.length &&
                `${Nova.formatNumber(first + 1)}-${Nova.formatNumber(
                    first + this.translations.length
                )} ${this.__('of')} ${Nova.formatNumber(this.allMatchingCount)}`
            )
        },

        pageParameter() {
            return 'page'
        },

        requestQueryString() {
            return {
                search: this.search,
                page: this.currentPage,
            }
        },
    },
}
</script>
