Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'translations.index',
            path: '/translations',
            component: require('./views/LocalesIndex'),
        },
        {
            name: 'translations.locales.create',
            path: '/translations/locales/create',
            component: require('./views/CreateLocale'),
        },
        {
            name: 'translations.locales.show',
            path: '/translations/locales/:locale',
            component: require('./views/ShowLocale'),
        },
        {
            name: 'translations.translations.create',
            path: '/translations/translations/:locale/create',
            component: require('./views/CreateTranslation'),
        },
    ])
})
