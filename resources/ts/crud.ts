import axios from "axios";
import trans from "./modules/trans";
import confirm from "./modules/confirm";
import "./vue";

if (!window.axios) {
    window.axios = axios;
    const metaCSRFToken = document.querySelector("meta[name='csrf-token']"),
        metacontent = metaCSRFToken?.getAttribute("content");

    if (!metaCSRFToken || !metacontent) {
        throw new Error("Missing meta CSRF token");
    }

    window.axios.defaults.headers.common = {
        Accept: "application/json, text/plain, */*",
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": metacontent,
    };
}
if (!window.__CRUD) {
    window.__CRUD = {
        _asset: "",
        _locale: "",
        _routes: [] as never as Record<string, string>,
        _translations: {
            json: [] as never as Record<string, string>,
            php: [] as never as Record<string, string>,
        },
        confirmDelete: function (this: HTMLFormElement, ev: SubmitEvent) {
            ev;
        },
        loadonceCallbacks: null 
    };
}

window.__CRUD.confirmDelete = function (
    this: HTMLFormElement,
    ev: SubmitEvent
) {
    const self = ev.target as HTMLFormElement | null;
    ev.preventDefault();

    if (!self) {
        throw new Error("CRUD confirmDelete EVENT target is null");
    }

    if (self.tagName !== "FORM") {
        throw new Error("CRUD confirmDelete EVENT target must be a html form");
    }

    confirm.methods.confirm(
        trans.methods.__("crud.confirm"),
        trans.methods.__("crud.confirm_ask"),
        self,
        (result) => {
            if (result.isConfirmed) {
                self.submit();
            }
        },
        {
            icon: "question",
            confirmButtonText: trans.methods.__("crud.delete"),
            showCancelButton: true,
            cancelButtonText: trans.methods.__("crud.cancel"),
            customClass: {
                confirmButton: "btn btn-danger mx-1",
                cancelButton: "btn btn-primary mx-1",
            },
        }
    );
};
window.addEventListener("DOMContentLoaded", () => {
    const elements = document.getElementsByClassName(
        "CrudConfirmDelete"
    ) as HTMLCollectionOf<HTMLFormElement>;
    for (const element of elements) {
        element.addEventListener("submit", window.__CRUD.confirmDelete);
    }
});
