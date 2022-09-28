import { App, createApp } from "vue";

document.addEventListener("DOMContentLoaded", () => {
    const setupApp = (app: App<Element>) => {
        app.config.globalProperties.window = window;
        app.config.globalProperties.document = document;
    };

    // Register components
    const vues = require.context("./components", true, /\.vue$/i);
    vues.keys().map((key: string) => {
        let htmlKey = key.split("/").pop();
        if (!htmlKey) {
            throw new Error(`Cannot load ${key}`);
        }
        htmlKey = htmlKey
            .split(".")[0]
            .replace(/\.?([A-Z])/g, function (x, y) {
                return "-" + y.toLowerCase();
            })
            .replace(/^-/, "")
            .replace("-component", "");

        const el = document.getElementById(htmlKey),
            elGroup = document.getElementsByClassName(htmlKey);
        // * Init using ID
        if (el) {
            const app = createApp(vues(key).default, {
                json: el.dataset.json ?? {},
            });
            setupApp(app);
            app.mount(el);
        }
        // * Init Using class
        for (const grpEl of elGroup) {
            // * Hack to prevent loop weirdness
            setTimeout(() => {
                const app = createApp(vues(key).default, {
                    json: (grpEl as HTMLElement).dataset.json ?? {},
                });
                setupApp(app);
                app.mount(grpEl);
            }, 1000);
        }
    });
});
