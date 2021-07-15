export default {
    methods: {
        route(routeName) {
            return Object.keys(window._routes).indexOf(routeName) !== -1 ? window._routes[routeName] : null;
        }
    },
}

