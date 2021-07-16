export default {
    methods: {
        route(routeName) {
            return Object.keys(window.__CRUD._routes).indexOf(routeName) !== -1 ? window.__CRUD._routes[routeName] : null;
        }
    },
}

