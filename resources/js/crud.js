window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
require('./vuejs')

window.__CRUD = {
    confirmDelete: (e) => {
        let self = e.target
        e.preventDefault();
        Swal.fire({
            title: 'Confirmez',
            text: 'Voulez-vous vraiment supprimer cet élément ?',
            icon: 'question',
            confirmButtonText: 'Supprimer',
            showCancelButton: true,
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                self.submit()
            }
        })
    }
}
