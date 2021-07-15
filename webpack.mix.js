const path = require('path');
const mix = require('laravel-mix');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
require('laravel-mix-polyfill');

mix.webpackConfig({
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

mix.js("resources/js/crud.js", "public/js/crud.js")
    .sass("resources/sass/crud.scss", "public/css/crud.css")
    .vue()
    .polyfill({
        enabled: true,
        useBuiltIns: "usage",
        targets: { "firefox": "50", "ie": 11 }
    });
