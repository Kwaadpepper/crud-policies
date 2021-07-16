export default {
    data() {
        return {
            locale: window.__CRUD._locale
        };
    },
    methods: {
        /**
         * Translate the given key.
         */
        __(key, replace) {
            let translation, translationNotFound = true, vars;

            try {
                translation = key.split('.').reduce((t, i) => t[i] || null, window.__CRUD._translations.php)
                if (translation) {
                    translationNotFound = false;
                }
            } catch (e) {
                translation = key;
            }
            if (translationNotFound && window.__CRUD._translations['json']) {
                translation = window.__CRUD._translations['json'][key] ? window.__CRUD._translations['json'][key] : key;
            }
            vars = translation.match(/:[0-9A-Za-z_]*/g);
            if (vars) {
                for (let match of vars) {
                    try {
                        translation = translation.replace(new RegExp(match, 'g'), replace[match.substr(1)]);
                    } catch (e) {
                        console.error('String replace failed ', e);
                    }
                }
            }

            return translation
        }
    },
}

