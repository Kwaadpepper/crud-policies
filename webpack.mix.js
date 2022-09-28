const path = require("path");
const mix = require("laravel-mix");

require("laravel-mix-polyfill");
require("laravel-mix-clean");
require("laravel-mix-eslint");
require("laravel-mix-stylelint");

const polyfill = {
    enabled: true,
    bugfixes: true,
    useBuiltIns: "usage",
    targets: "last 3 version, not dead, >0.3%",
};

mix.webpackConfig({
    mode: process.env.NODE_ENV,
    resolve: {
        extensions: ["*", ".js", ".vue", ".ts"],
        alias: {
            "@": path.resolve("resources/assets"),
            vue$: "vue/dist/vue.runtime.esm-bundler.js",
        },
    },
    module: {
        rules: [
            {
                test: /\.ts$/,
                loader: "ts-loader",
                options: {
                    appendTsSuffixTo: [/\.vue$/],
                },
            },
            {
                test: /\.vue$/,
                loader: "vue-loader",
                options: {
                    esModule: true,
                },
            },
        ],
    },
    output: {
        publicPath: '/crud-policies/vendor/',
    },
    // Pour debug compilation errors
    // stats: {
    //     children: true,
    //     warningsFilter: [/\-\-underline\-color/],
    // },
});

mix.clean({
    cleanOnceBeforeBuildPatterns: [
        'css/*',
        'js/*'
    ]
});

mix.options({
    postCss: [
        require("postcss-discard-comments")({
            removeAll: true,
        }),
    ],
});

mix.ts("resources/ts/crud.ts", "crud-policies/js/crud.js", { transpileOnly: true })
    .ts("resources/ts/ckeditor.ts", "crud-policies/js/ckeditor.js", { transpileOnly: true })
    .vue({
        runtimeOnly: true,
        extractStyles: true,
        globalStyles: false,
    })
    .eslint({
        fix: true,
        extensions: ["js", "ts"],
    })
    .stylelint({
        configFile: ".stylelintrc.json",
        files: ["**/*.scss"],
    })
    .sass("resources/sass/crud.scss", "crud-policies/css/crud.css")
    .polyfill(polyfill)
    .sourceMaps(false, "inline-source-map")
