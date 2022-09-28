<template>
  <div>
    <VueSelect
      :id="name"
      ref="select"
      :loading="loading"
      v-model="values"
      :clearable="true"
      label="label"
      :options="data"
      @search="search"
      multiple
      :disabled="dReadonly || dDisabled"
      :filterable="true"
      :searchable="true"
    >
      <template #list-footer>
        <li class="pagination">
          <button
            @click="
              $event.preventDefault();
              page -= 1;
            "
            :disabled="!hasPrevPage"
            class="btn btn-sm btn-primary mt-2 mb-2 ms-2 me-2"
          >
            {{ __("crud.previous") }}
          </button>
          <button
            @click="
              $event.preventDefault();
              page += 1;
            "
            :disabled="!hasNextPage"
            class="btn btn-sm btn-primary mt-2 mb-2"
          >
            {{ __("crud.next") }}
          </button>
        </li>
      </template>
    </VueSelect>
    <input
      type="hidden"
      :name="name + '[]'"
      v-for="item in values"
      :key="item.value"
      :value="item.value"
    >
  </div>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import route from "../modules/route";
import trans from "../modules/trans";
import confirm from "../modules/confirm";
import VueSelect from "vue-select";

export default defineComponent({
  components: {
    VueSelect,
  },
  mixins: [route, trans, confirm],
  mounted() {
    const json = String(this.$attrs.json ?? "{}"),
          data = JSON.parse(json);
    this.name = data.name;
    this.tablename = data.tablename;

    this.dDisabled = !!data.disabled;
    this.dReadonly = !!data.readonly;
    let values = data.values;
    for (let k in values) {
      this.values.push(values[k]);
    }
    this.search(false);
  },
  data(): {
    name: string;
    tablename: string;
    dDisabled: boolean;
    dReadonly: boolean;
    values: VselectOption[];
    loading: boolean;
    searchText: string;
    data: LaravelModelList;
    page: number;
    perPage: number;
    hasNextPage: boolean;
    hasPrevPage: boolean;
  } {
    return {
      name: "",
      tablename: "",
      dDisabled: false,
      dReadonly: false,
      values: [],
      loading: false,
      searchText: "",
      data: [],
      page: 1,
      perPage: 15,
      hasNextPage: true,
      hasPrevPage: false,
    };
  },
  watch: {
    page() {
      if (this.page < 1) {
        this.page = 1;
      }
      if (this.page > 1) {
        this.hasPrevPage = true;
      } else {
        this.hasPrevPage = false;
      }
      this.search();
    },
    data() {
      if (this.data.length == this.perPage) {
        this.hasNextPage = true;
      } else {
        this.hasNextPage = false;
      }
    },
  },
  methods: {
    search(search: false | null | string = null): void {
      if (search !== false && search !== null) {
        this.searchText = search;
      }
      let init = search === false ? true : false,
          routeUrl = this.route(`${this.tablename}.${this.name}.model.index`);

      if (!routeUrl) {
        throw new Error(
          `CRUD undefined route ${this.tablename}.${this.name}.model.index`
        );
      }

      routeUrl += "?page=" + this.page;
      routeUrl += "&s=" + this.searchText;
      this.loading = true;
      window.axios
        .get(routeUrl)
        .then((response) => {
          if (init && this.data.length) {
            this.data = response.data;
          }
        })
        .catch((e) => {
          let message =
            "Erreur lors de l'opération, merci d'actualiser la page.";
          if (e.response?.status === 422) {
            const errors = e.response?.data?.errors;
            if (errors instanceof String) {
              message = errors as string;
            }
            if (errors instanceof Object) {
              message = Object.values(errors).reduce(function (
                previous,
                current
              ) {
                return String(previous) + String(current);
              }) as string;
            }
            this.error(message);
            console.warn(errors);
          }
        })
        .then(() => {
          this.loading = false;
        });
    },
  },
});
</script>

<style lang="scss">
@import "../../../node_modules/vue-select/dist/vue-select.css";
</style>
