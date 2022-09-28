export default {
    methods: {
        asset(path: string) {
            return `${window.__CRUD._asset}${path}`;
        },
    },
};
