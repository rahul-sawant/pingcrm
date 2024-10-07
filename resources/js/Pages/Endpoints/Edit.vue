<template>
  <div>

    <Head :title="form.title" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/endpoints">Endpoints</Link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.title }}
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.title" :error="form.errors.title" class="pb-8 pr-6 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.uuid" :error="form.errors.uuid" class="pb-8 pr-6 w-full lg:w-1/2" label="UUID" />
          <text-input v-model="form.location" :error="form.errors.location" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Location" />
          <text-input v-model="form.stream_key" :error="form.errors.stream_key" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Stream Key" />
          <text-input v-model="form.ip_addr" :error="form.errors.ip_addr" class="pb-8 pr-6 w-full lg:w-1/2"
            label="IP Address" />
          <text-input v-model="form.port" :error="form.errors.port" class="pb-8 pr-6 w-full lg:w-1/2" label="Port" />
          <select-input v-model="form.organization" :error = "form.errors.organization" class="pb-8 pr-6 w-full lg:w-1/2"
            label="Organization">
            <option value="" disabled></option>
            <option v-for="organization in organizations" :key="organization.id" :value="organization.id" :selected="organization.id == form.organization">
              {{ organization.name }}</option>
          </select-input>
        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <button class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete
            Endpoint</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Update
            Endpoint</loading-button>
        </div>
      </form>
    </div>
    <h2 class="mt-12 text-2xl font-bold">Streams</h2>
    <div class="mt-6 bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">Name</th>
          <th class="pb-4 pt-6 px-6" colspan="2">Description</th>
        </tr>
        <tr v-for="stream in endpoint.streams" :key="contact.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/contacts/${contact.id}/edit`">
            {{ contact.name }}
            <icon v-if="contact.deleted_at" name="trash" class="shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/contacts/${contact.id}/edit`" tabindex="-1">
            {{ contact.city }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/contacts/${contact.id}/edit`" tabindex="-1">
            {{ contact.phone }}
            </Link>
          </td>
          <td class="w-px border-t">
            <Link class="flex items-center px-4" :href="`/contacts/${contact.id}/edit`" tabindex="-1">
            <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </Link>
          </td>
        </tr>
        <tr v-if="endpoint.streams.length === 0">
          <td class="px-6 py-4 border-t" colspan="4">No streams found.</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import {
  Head,
  Link
} from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import TrashedMessage from '@/Shared/TrashedMessage.vue'

export default {
  components: {
    Head,
    Icon,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    endpoint: Object,
    organizations: Array,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        title: this.endpoint.title,
        uuid: this.endpoint.uuid,
        location: this.endpoint.location,
        stream_key: this.endpoint.stream_key,
        ip_addr: this.endpoint.ip_addr,
        port: this.endpoint.port,
        organization: this.endpoint.organization,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/endpoints/${this.endpoint.id}`)
    },
    destroy() {
      if (confirm('Are you sure you want to delete this endpoint?')) {
        this.$inertia.delete(`/endpoints/${this.endpoint.id}`)
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this endpoint?')) {
        this.$inertia.put(`/endpoints/${this.endpoint.id}/restore`)
      }
    },
  },
}
</script>
