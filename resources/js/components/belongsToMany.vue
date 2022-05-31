<template>
    <div>
        <v-select
            :id="name"
            ref="select"
            :loading="loading"
            v-model="values"
            :clearable="true"
            label="label"
            :options='data'
            @search="search"
            multiple
            :disabled="dReadonly||dDisabled"
            :filterable="true"
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
        <input
            type="hidden"
            :name="name+'[]'"
            v-for="item in values" :key="item.value"
            :value="item.value"/>
    </div>
</template>

<script>
export default {
    mounted() {
        let data = JSON.parse(this.$parent.$options.json)
        this.name = data.name
        this.tablename = data.tablename
        this.dDisabled = !!data.disabled
        this.dReadonly = !!data.readonly
        let values = data.values
        for(let k in values) {
            this.values.push(values[k])
        }
        this.search(false)
    },
    data() {
        return {
            name: '',
            tablename: '',
            dDisabled : false,
            dReadonly : false,
            values: [],
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
