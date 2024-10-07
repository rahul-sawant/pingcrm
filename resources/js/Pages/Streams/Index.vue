<template>
  <div>
    <Head title="Streams" />
    <h1 class="mb-8 text-3xl font-bold">Streams</h1>
    <div class="flex items-center justify-between mb-6">
      <search v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset"/>
      <Link class="btn-indigo" href="/streams/create">
        <span>Create</span>
        <span class="hidden md:inline">&nbsp;Streams</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <thead>
          <tr class="text-left font-bold">
            <th class="pb-4 pt-6 px-6">UID</th>
            <th class="pb-4 pt-6 px-6">UUID</th>
            <th class="pb-4 pt-6 px-6">Name</th>
            <th class="pb-4 pt-6 px-6">Slug</th>
            <th class="pb-4 pt-6 px-6">Description</th>
            <th class="pb-4 pt-6 px-6">Organization</th>
            <th class="pb-4 pt-6 px-6">Endpoint</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="stream in streams.data" :key="stream.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/streams/${stream.id}/edit`">
                {{ stream.id }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/streams/${stream.id}/edit`" tabindex="-1">
                {{ stream.uuid }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/streams/${stream.id}/edit`" tabindex="-1">
                {{ stream.name }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/streams/${stream.id}/edit`" tabindex="-1">
                {{ stream.slug }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/streams/${stream.id}/edit`" tabindex="-1">
                {{ truncateText(stream.description) }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/endpoints/${stream.id}/edit`" tabindex="-1">
                {{ stream.organization}}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/endpoints/${stream.id}/edit`" tabindex="-1">
                {{ stream.endpoint }}
              </Link>
            </td>
            <td class="w-px border-t">
              <Link class="flex items-center px-4" :href="`/endpoints/${stream.id}/edit`" tabindex="-1">
                <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
              </Link>
            </td>
          </tr>
          <tr v-if="streams.data.length === 0">
            <td class="px-6 py-4 border-t" colspan="8">No Streams found.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <pagination class="mt-6" :links="streams.links" />
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
    streams: Object,
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
        this.$inertia.get('/streams', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    truncateText(text, length = 30) {
      return text.length > length ? text.substring(0, length) + '...' : text
    },
  },
}
</script>
