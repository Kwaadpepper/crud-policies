import axios from 'axios';

class CKUploadAdaptater {
    constructor(loader) {
        // The file loader instance to use during the upload.
        this.loader = loader;
    }

    // Starts the upload process.
    upload() {
        return this.loader.file
            .then(file => new Promise((resolve, reject) => {
                this._initRequest();
                this._sendRequest(file, resolve, reject);
            }));
    }

    // Aborts the upload process.
    abort() {
        if (this.cancel) {
            this.cancel.abort();
        }
    }

    // Initializes the XMLHttpRequest object using the URL passed to the constructor.
    _initRequest() {
        this.cancel = axios.CancelToken.source();
        this.route = require('./route').default.methods.route('crud-policies.upload')
    }

    // Prepares the data and sends the request.
    _sendRequest(file, resolve, reject) {
        // Prepare the form data.
        const data = new FormData();
        data.append('upload', file);
        axios.post(this.route, data, {
            withCredentials: true,
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            onUploadProgress: (evt) => {
                this.loader.uploadTotal = evt.total;
                this.loader.uploaded = evt.loaded;
            }
        }).then((response) => {
            resolve({
                default: response.data.url
            })
        }).catch((error) => {
            console.error(error);
            reject('Le fichier à été marbré')
        })
    }
}

export { CKUploadAdaptater }
