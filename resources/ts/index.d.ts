export {};

import { AxiosStatic } from "axios";
import Swal from "sweetalert2";
// import type { VueSelectProps } from "vue-select";

type SwalType = typeof Swal;

type EnumType = {
    label: string;
    value: number | string;
};

type VselectOptionType = {
    label: string;
    value: number | string;
};

// * Recursive List<string>
type StringList = Record<PropertyKey, string>;
// eslint-disable-next-line @typescript-eslint/no-empty-interface
interface NestedStringListInterface
    extends Record<PropertyKey, NestedStringListInterface | StringList> {}

// * Recursive List<number>
type NumberList = Record<PropertyKey, number>;
// eslint-disable-next-line @typescript-eslint/no-empty-interface
interface NestedNumberListInterface
    extends Record<PropertyKey, NestedNumberListInterface | NumberList> {}

// * Recursive List<Model>
type Model = Record<
    string,
    | string
    | boolean
    | number
    | object
    | NestedStringList
    | NestedNumberList
    | NestedModelInterface
>;
// eslint-disable-next-line @typescript-eslint/no-empty-interface
interface NestedModelInterface extends Model {}

type NestedStringList = NestedStringListInterface | StringList | string;
type NestedNumberList = NestedNumberListInterface | NumberList | number;
type ModelList = Array<Model>;

declare global {
    type ValueOf<T> = T[keyof T];
    type CustomTag = LaravelModel;
    type LaravelModel = Model;
    type LaravelModelList = ModelList;

    type Enum = EnumType;
    type VselectOption = VselectOptionType;

    interface Window {
        axios: AxiosStatic;
        Swal: SwalType;
        __CRUD: {
            _asset: string;
            _locale: string;
            _routes: Record<string, string>;
            _translations: {
                json: NestedStringList;
                php: NestedStringList;
            };
            confirmDelete: (this: HTMLFormElement, ev: SubmitEvent) => any,
            loadonceCallbacks: Array<() => void> | null
        };
    }
}
