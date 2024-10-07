<template>
  <div>
    <Head title="Create Endpoints" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/endpoints">Endpoints</Link>
      <span class="text-indigo-400 font-medium">/</span> Create
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.title" :error="form.errors.title" class="pb-8 pr-6 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.uuid" :error="form.errors.uuid" class="pb-8 pr-6 w-full lg:w-1/2" label="UUID"/>
          <text-input v-model="form.location" :error="form.errors.location" class="pb-8 pr-6 w-full lg:w-1/2" label="Location" />
          <text-input v-model="form.stream_key" :error="form.errors.stream_key" class="pb-8 pr-6 w-full lg:w-1/2" label="Stream Key" />
          <text-input v-model="form.ip_addr" :error="form.errors.ip_addr" class="pb-8 pr-6 w-full lg:w-1/2" label="IP Address" />
          <text-input v-model="form.port" :error="form.errors.port" class="pb-8 pr-6 w-full lg:w-1/2" label="Port" />
          <select-input v-model="form.organization" :error="form.errors.organization" class="pb-8 pr-6 w-full lg:w-1/2" label="Organization">
            <option value="" disabled></option>
            <option v-for="organization in organizations" :key="organization.id" :value="organization.id">{{ organization.name }}</option>
          </select-input>
        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Create Endpoint</loading-button>
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
        location: null,
        stream_key: null,
        ip_addr: null,
        port: null,
        type: 'GO',
        organization: null,
      }),
    }
  },
  props: {
    organizations: Object,
  },
  methods: {
    store() {
      this.form.post('/endpoints')
    },
  },
}
</script>
