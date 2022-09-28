// SweetAlert
import Swal, { SweetAlertResult } from "sweetalert2";
import { SweetAlertOptions } from "sweetalert2";

import * as trans from "./trans";

export default {
    methods: {
        confirm(
            title: string,
            text = "",
            self: HTMLElement,
            after: (response: SweetAlertResult) => void,
            options: SweetAlertOptions = {}
        ) {
            if (!self) {
                throw Error("self is needed to set 'this' on callback");
            }
            if (!after) {
                throw Error("callback is needed when using confirm");
            }
            const icon = options.icon ?? "warning";
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonText:
                    options.confirmButtonText ??
                    trans.default.methods.__("Oui"),
                cancelButtonText:
                    options.cancelButtonText ??
                    trans.default.methods.__("Annuler"),
                reverseButtons: true,
                // * Bootstrap Styling
                customClass: {
                    confirmButton: "btn btn-lg btn-success ms-3 me-3",
                    cancelButton: "btn btn-lg btn-danger ms-3 me-3",
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    if (after) {
                        after.apply(self, [result]);
                    }
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    Swal.fire(
                        trans.default.methods.__(
                            "L'opération n'as pas été effectuée"
                        )
                    );
                }
            });
            return false;
        },
        error(title: string, text = "", options: SweetAlertOptions = {}) {
            const icon = options.icon ?? "error";
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                // * Bootstrap Styling
                customClass: {
                    confirmButton: "btn btn-lg btn-success ms-3 me-3",
                },
                buttonsStyling: false,
            });
            return false;
        },
    },
};
