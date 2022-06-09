const mix = require('laravel-mix');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
require('laravel-mix-polyfill');

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
    plugins: [
        new CleanWebpackPlugin({
            cleanOnceBeforeBuildPatterns: [
                'css/*',
                'js/*'
            ]
        })
    ],
});

mix.options({
    postCss: [
        require('postcss-discard-comments')({
            removeAll: true
        })
    ]
});

mix.js("resources/js/crud.js", "crud-policies/js/crud.js")
    .sass("resources/sass/crud.scss", "crud-policies/css/crud.css")
    .vue({ runtimeOnly: true })
    .polyfill({
        enabled: true,
        useBuiltIns: "usage",
        targets: { "firefox": "50", "ie": 11 }
    });
