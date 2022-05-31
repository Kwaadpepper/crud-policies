import Vue from 'vue'
import vSelect from "vue-select"
window.__VueCRUD = Vue;
__VueCRUD.component('v-select', vSelect)

__VueCRUD.filter('str_limit', function (value, size) {
    if (!value) return '';
    value = value.toString();
    if (value.length <= size) {
        return value;
    }
    return value.substr(0, size) + '...';
});


// register modules
const modules = require.context("./modules", true, /\.js$/i);
modules.keys().map(key => __VueCRUD.mixin(modules(key).default ?? modules(key)))

document.addEventListener('DOMContentLoaded', () => {
    // Register components
    const vues = require.context("./components", true, /\.vue$/i)
    vues.keys().map(key => {
        let htmlKey = key.split('/')
            .pop()
            .split('.')[0]
            .replace(/\.?([A-Z])/g, function (x, y) {
                return '-' + y.toLowerCase();
            })
            .replace(/^-/, '')
            .replace('-component', '')

        let el = document.getElementById(htmlKey),
            elGroup = document.getElementsByClassName(htmlKey)
        if (el) {
            new __VueCRUD({
                el: el,
                json: el.dataset.json ?? {},
                // * For runtime vuejs
                render: createElement => createElement(vues(key).default)
            })
        }
        for (let grpEl of elGroup) {
            // * Hack to prevent loop weirdness
            setTimeout(() => {
                new __VueCRUD({
                    el: grpEl,
                    json: grpEl.dataset.json,
                    // * For runtime vuejs
                    render: createElement => createElement(vues(key).default)
                })
            }, 1000)
        }
    })
})


// Register components
const vues = require.context("./components", true, /\.vue$/i);
vues.keys().map(key =>
    __VueCRUD.component(
        key
            .split("/")
            .pop()
            .split(".")[0]
            .replace(/\.?([A-Z])/g, function (x, y) {
                return "-" + y.toLowerCase();
            })
            .replace(/^-/, "")
            .replace("-component", ""),
        vues(key).default
    )
);
