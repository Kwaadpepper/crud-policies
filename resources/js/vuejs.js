window.Vue = require('vue').default;
import vSelect from "vue-select"
Vue.component('v-select', vSelect)

let vueOptions = {
    el: "#app"
};

// register modules
const modules = require.context("./modules", true, /\.js$/i);
modules.keys().map(key => Vue.mixin(modules(key).default));

// Register components
const vues = require.context("./components", true, /\.vue$/i);
vues.keys().map(key =>
    Vue.component(
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

const app = new Vue(vueOptions);

Vue.filter('str_limit', function (value, size) {
    if (!value) return '';
    value = value.toString();
    if (value.length <= size) {
        return value;
    }
    return value.substr(0, size) + '...';
});
