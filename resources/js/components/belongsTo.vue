<template>
    <div>
        <v-select
            :id="name"
            ref="select"
            :loading="loading"
            v-model="value"
            :clearable="false"
            label="label"
            :options='data'
            @search="search"
            :filterable="true"
            :disabled="dReadonly||dDisabled"
            :searchable="true">
            <li slot="list-footer" class="pagination">
                <button
                    @click="$event.preventDefault(); page -= 1"
                    :disabled="!hasPrevPage"
                    class="btn btn-sm btn-primary mt-2 mb-2 ms-2 me-2">{{ __('crud.previous') }}</button>
                <button
                    @click="$event.preventDefault(); page += 1"
                    :disabled="!hasNextPage"
                    class="btn btn-sm btn-primary mt-2 mb-2">{{ __('crud.next') }}</button>
            </li>
        </v-select>
        <input type="hidden" v-model="value.value" :name="name" />
    </div>
</template>

<script>
export default {
    mounted() {
        this.dDisabled = !!this.disabled
        this.dReadonly = !!this.readonly
        this.value = JSON.parse(this.dbValue)
        if(!this.value) {
            this.value = {
                label: null,
                value: null
            }
        }
        this.search(false)
    },
    props: [
        'disabled',
        'readonly',
        'tablename',
        'name',
        'dbValue'
    ],
    data() {
        return {
            dDisabled : false,
            dReadonly : false,
            value: {
                label: null,
                value: null
            },
            loading: false,
            searchText: '',
            data: [],
            page: 1,
            perPage: 15,
            hasNextPage: true,
            hasPrevPage: false
        }
    },
    watch: {
        page() {
            if(this.page < 1) {
                this.page = 1
            }
            if(this.page > 1) {
                this.hasPrevPage = true
            } else {
                this.hasPrevPage = false
            }
            this.search()
        },
        data() {
            if(this.data.length == this.perPage) {
                this.hasNextPage = true
            } else {
                this.hasNextPage = false
            }
        }
    },
    methods: {
        search (search = null) {
            if(search !== false && search !== null) {
                this.searchText = search
            }
            let init = search === false ? true : false
            let route = this.route(this.tablename + '.' + this.name + '.model.index')
            route += '?page='+this.page
            route += '&s='+this.searchText
            this.loading = true
            axios.get(route)
            .then(response => {
                this.data = response.data
                if (init && this.data.length && this.value.value == null) {
                    this.value = this.data[0]
                }
            })
            .catch(error => {
                console.log(error);
            }).then(() => {
                this.loading = false
            })
        },
    }
}
</script>
