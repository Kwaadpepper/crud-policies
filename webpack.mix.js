const path = require('path');
const mix = require('laravel-mix');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
require('laravel-mix-polyfill');

mix.webpackConfig({
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
    .vue()
    .polyfill({
        enabled: true,
        useBuiltIns: "usage",
        targets: { "firefox": "50", "ie": 11 }
    });
