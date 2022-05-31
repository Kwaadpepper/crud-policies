if (!window.axios) {
    window.axios = require('axios')
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
    let csrfToken = document.querySelector('meta[name="csrf-token"]')
    if (csrfToken.getAttribute('content')) {
        window.axios.defaults.headers.common['Authorization'] = `Bearer ${csrfToken.getAttribute('content')}`;
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content')
    }
}
if (!window.__CRUD) {
    window.__CRUD = {}
}
require('./vuejs')
const trans = require('./modules/trans.js').default.methods;
window.__CRUD.confirmDelete = (e) => {
    let self = e.target
    e.preventDefault();
    Swal.fire({
        title: trans.__('crud.confirm'),
        text: trans.__('crud.confirm_ask'),
        icon: 'question',
        confirmButtonText: trans.__('crud.delete'),
        showCancelButton: true,
        cancelButtonText: trans.__('crud.cancel'),
        customClass: {
            confirmButton: 'btn btn-danger mx-1',
            cancelButton: 'btn btn-primary mx-1',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            self.submit()
        }
    })
}
