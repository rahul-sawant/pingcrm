<template>
  <div>
    <Head title="Create Streams" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/streams">Streams</Link>
      <span class="text-indigo-400 font-medium">/</span> Create
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.title" :error="form.errors.title" class="pb-8 pr-6 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.uuid" :error="form.errors.uuid" class="pb-8 pr-6 w-full lg:w-1/2" label="UUID"/>
          <text-input v-model="form.description" :error="form.errors.description" class="pb-8 pr-6 w-full lg:w-1/2" label="Description" />
          <select-input v-model="form.endpoint" :error="form.errors.endpoint" class="pb-8 pr-6 w-full lg:w-1/2" label="Endpoint">
            <option value="" disabled></option>
            <option v-for="endpoint in endpoints" :key="endpoint.id" :value="endpoint.id">{{ endpoint.title }}</option>
          </select-input>
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Create Stream</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
  },
  layout: Layout,
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        title: null,
        uuid: null,
        description: null,
        endpoint: null,
      }),
    }
  },
  props: {
    endpoints: Object,
  },
  methods: {
    store() {
      this.form.post('/streams')
    },
  },
}
</script>
