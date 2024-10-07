<template>
  <div>
    <Head title="Endpoints" />
    <h1 class="mb-8 text-3xl font-bold">Endpoints</h1>
    <div class="flex items-center justify-between mb-6">
      <search v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset"/>
      <Link class="btn-indigo" href="/endpoints/create">
        <span>Create</span>
        <span class="hidden md:inline">&nbsp;Endpoints</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <thead>
          <tr class="text-left font-bold">
            <th class="pb-4 pt-6 px-6">UID</th>
            <th class="pb-4 pt-6 px-6">UUID</th>
            <th class="pb-4 pt-6 px-6">Name</th>
            <th class="pb-4 pt-6 px-6">Location</th>
            <th class="pb-4 pt-6 px-6">Stream-Key</th>
            <th class="pb-4 pt-6 px-6">IP</th>
            <th class="pb-4 pt-6 px-6">Port</th>
            <th class="pb-4 pt-6 px-6">Organization</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="endpoint in endpoints.data" :key="endpoints.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/endpoints/${endpoint.id}/edit`">
                {{ endpoint.id }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/endpoints/${endpoint.id}/edit`" tabindex="-1">
                {{ endpoint.uuid }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/endpoints/${endpoint.id}/edit`" tabindex="-1">
                {{ endpoint.name }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/endpoints/${endpoint.id}/edit`" tabindex="-1">
                {{ endpoint.location }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/endpoints/${endpoint.id}/edit`" tabindex="-1">
                {{ "xxx-xxx-xxx-"+endpoint.stream_key.slice(-4) }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/endpoints/${endpoint.id}/edit`" tabindex="-1">
                {{ endpoint.ip_addr}}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/endpoints/${endpoint.id}/edit`" tabindex="-1">
                {{ endpoint.port }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/endpoints/${endpoint.id}/edit`" tabindex="-1">
                {{ endpoint.organization }}
              </Link>
            </td>
            <td class="w-px border-t">
              <Link class="flex items-center px-4" :href="`/endpoints/${endpoint.id}/edit`" tabindex="-1">
                <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
              </Link>
            </td>
          </tr>
          <tr v-if="endpoints.data.length === 0">
            <td class="px-6 py-4 border-t" colspan="8">No endpoints found.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <pagination class="mt-6" :links="endpoints.links" />
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout.vue'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination.vue'
import Search from '@/Shared/Search.vue'

export default {
  components: {
    Head,
    Icon,
    Link,
    Pagination,
    Search,
  },
  layout: Layout,
  props: {
    filters: Object,
    endpoints: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/endpoints', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
