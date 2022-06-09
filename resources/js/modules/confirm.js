import Swal from 'sweetalert2/dist/sweetalert2.js'

export default {
    methods: {
        confirm(title, text = '', self, after, options = {}) {
            if (!self) {
                throw Error('self is needed to set "this" on callback');
            }
            if (!after) {
                throw Error('callback is needed when using confirm');
            }
            let icon = options.icon ?? 'warning'
            let confirm = options.confirm ?? 'Oui'
            let cancel = options.confirm ?? 'Annuler'
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonText: confirm,
                cancelButtonText: cancel,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (after) {
                        after.apply(self, [result])
                    }
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    Swal.fire('L\'opération n\'as pas été effectuée')
                }
            })
            return false;
        }
    },
}

