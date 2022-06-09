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
const trans = require('./modules/trans.js').default.methods
const confirmJS = require('./modules/confirm.js').default.methods.confirm
window.__CRUD.confirmDelete = (e) => {
    let self = e.target
    e.preventDefault()
    confirmJS(
        trans.__('crud.confirm'),
        trans.__('crud.confirm_ask'),
        self,
        (result) => {
            if (result.isConfirmed) {
                self.submit()
            }
        },
        {
            icon: 'question',
            confirmButtonText: trans.__('crud.delete'),
            showCancelButton: true,
            cancelButtonText: trans.__('crud.cancel'),
            customClass: {
                confirmButton: 'btn btn-danger mx-1',
                cancelButton: 'btn btn-primary mx-1',
            }
        }
    )
}
window.addEventListener('DOMContentLoaded', () => {
    let elements = document.getElementsByClassName('CrudConfirmDelete')
    for (let element of elements) {
        element.addEventListener('submit', window.__CRUD.confirmDelete)
    }
})
