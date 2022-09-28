const route = {
    methods: {
        route(
            routeName: string,
            parameters: Record<string, number | string> = {}
        ): string | null {
            const routes = route.flattenProperties(window.__CRUD._routes);
            const routeUrl =
                Object.keys(routes).indexOf(routeName) !== -1
                    ? routes[routeName] as string
                    : null;
            if (!routeUrl) {
                return routeUrl;
            }
            // copy parameters
            const iParameters = Object.assign({}, parameters),
                origin = window.location.origin,
                hash = window.location.hash;
            let pathname = routeUrl.replace(origin, ""),
                search = window.location.search;

            // Replace in path name.
            for (const paramName in iParameters) {
                const paramValue = parameters[paramName];
                if (pathname.indexOf(paramName) !== -1) {
                    pathname = pathname.replace(
                        paramName,
                        paramValue as string
                    );
                }
                delete parameters[paramName];
            }

            for (const paramName in parameters) {
                const paramValue = parameters[paramName];
                if (search.indexOf(paramName + "=") >= 0) {
                    const prefix = search.substring(
                        0,
                        search.indexOf(paramName + "=")
                    );
                    let suffix = search.substring(
                        search.indexOf(paramName + "=")
                    );
                    suffix = suffix.substring(suffix.indexOf("=") + 1);
                    suffix =
                        suffix.indexOf("&") >= 0
                            ? suffix.substring(suffix.indexOf("&"))
                            : "";
                    search = `${prefix}${paramName}=${paramValue}${suffix}`;
                } else if (search.indexOf("?") < 0) {
                    search += `?${paramName}=${paramValue}`;
                } else {
                    search += `&${paramName}=${paramValue}`;
                }
            }
            return `${origin}${pathname}${search}${hash}`;
        },
    },
    flattenProperties(
        obj: Record<PropertyKey, unknown>,
        parent: string | null = null,
        res: Record<PropertyKey, unknown> = {}
    ): Record<PropertyKey, unknown> {
        for (const key of Object.keys(obj)) {
            const propName = parent ? parent + "." + key : key;
            if (obj[key] instanceof Object) {
                route.flattenProperties(
                    obj[key] as Record<PropertyKey, unknown>,
                    propName,
                    res
                );
            } else {
                res[propName] = obj[key];
            }
        }
        return res;
    },
};

export default route;
