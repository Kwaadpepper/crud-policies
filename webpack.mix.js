const mix = require('laravel-mix')
require('laravel-mix-clean')
require('laravel-mix-polyfill')

const polyfill = {
    enabled: true,
    useBuiltIns: "usage",
    // legacy targets from gulp file
    targets: ['last 3 version', 'safari 5', 'ie 8', 'opera 12.1', 'ios 6', 'android 4']
}

mix.webpackConfig({
    mode: process.env.NODE_ENV,
    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.runtime.esm.js',
        }
    },
    output: {
        publicPath: '/crud-policies/vendor/',
    },
    // Pour debug compilation errors
    // stats: {
    //     children: true,
    //     warningsFilter: [
    //         /\-\-underline\-color/,
    //     ]
    // },
})

mix.clean({
    cleanOnceBeforeBuildPatterns: [
        'css/*',
        'js/*'
    ]
})

mix.options({
    postCss: [
        require('postcss-discard-comments')({
            removeAll: true
        })
    ]
})

mix.js("resources/js/crud.js", "crud-policies/js/crud.js")
    .vue({
        runtimeOnly: true,
        extractStyles: true,
        globalStyles: false
    })
    .sass("resources/sass/crud.scss", "crud-policies/css/crud.css")
    .polyfill({
        enabled: true,
        useBuiltIns: "usage",
        targets: { "firefox": "50", "ie": 11 }
    }).sourceMaps(true)
