if (!window.axios) {
    window.axios = require('axios');
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
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
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            self.submit()
        }
    })
}
