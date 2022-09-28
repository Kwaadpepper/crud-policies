import axios from "axios";
import type { CancelTokenSource } from "axios";
import route from "./route";

type CKLoader = {
    file: Promise<File | null>;
    uploadTotal: number | null;
    uploaded: number;
};

class CKUploadAdaptater {
    route: string;
    cancel: CancelTokenSource;
    loader: CKLoader;

    constructor(loader: CKLoader) {
        // The file loader instance to use during the upload.
        this.loader = loader;

        const routeUrl = route.methods.route("crud-policies.upload");
        if (!routeUrl) {
            throw new Error("CRUD undefined route crud-policies.upload");
        }
        this.route = routeUrl;
        this.cancel = axios.CancelToken.source();
    }

    // Starts the upload process.
    upload() {
        return this.loader.file.then(
            (file: File | null) =>
                new Promise((resolve, reject) => {
                    this._initRequest();
                    if (file) {
                        this._sendRequest(file, resolve, reject);
                    }
                })
        );
    }

    // Aborts the upload process.
    abort() {
        if (this.cancel) {
            this.cancel.cancel();
        }
    }

    // Initializes the XMLHttpRequest object using the URL passed to the constructor.
    _initRequest() {
        this.cancel = axios.CancelToken.source();
    }

    // Prepares the data and sends the request.
    _sendRequest(
        file: File,
        resolve: (value: unknown) => void,
        reject: (value: unknown) => void
    ) {
        // Prepare the form data.
        const data = new FormData();
        data.append("upload", file);
        const headers = Object.assign(
            {},
            window.axios ? window.axios.defaults.headers.common : {}
        );
        headers["Content-Type"] = "multipart/form-data";
        axios
            .post(this.route, data, {
                withCredentials: true,
                headers: headers,
                onUploadProgress: (evt) => {
                    this.loader.uploadTotal = evt.total;
                    this.loader.uploaded = evt.loaded;
                },
            })
            .then((response) => {
                resolve({
                    default: response.data.url,
                });
            })
            .catch((error) => {
                console.error(error);
                reject("Le fichier à été marbré");
            });
    }
}

export { CKUploadAdaptater };
