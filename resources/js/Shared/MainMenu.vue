<template>
  <div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/">
      <icon name="dashboard" class="mr-2 w-4 h-4"
        :class="isUrl('') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
      <div :class="isUrl('') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Dashboard</div>
      </Link>
    </div>
    <div v-if="auth.user.owner" class="mb-4">
      <Link class="group flex items-center py-3" href="/organizations">
      <icon name="office" class="mr-2 w-4 h-4"
        :class="isUrl('organizations') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
      <div :class="isUrl('organizations') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Organizations</div>
      </Link>
    </div>
    <div v-if="auth.user.owner" class="mb-4">
      <Link class="group flex items-center py-3" href="/endpoints">
      <icon name="endpoints" class="mr-2 w-4 h-4"
        :class="isUrl('endpoints') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
      <div :class="isUrl('endpoints') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Endpoints</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="/streams">
      <icon name="streams" class="mr-2 w-4 h-4"
        :class="isUrl('streams') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
      <div :class="isUrl('streams') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Streams</div>
      </Link>
    </div>
    <div v-if="auth.user.owner" class="mb-4">
      <Link class="group flex items-center py-3" href="/roles">
      <icon name="roles" class="mr-2 w-4 h-4"
        :class="isUrl('roles') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
      <div :class="isUrl('roles') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Roles</div>
      </Link>
    </div>
    <div v-if="auth.user.owner" class="mb-4">
      <Link class="group flex items-center py-3" href="/permissions">
      <icon name="permissions" class="mr-2 w-4 h-4"
        :class="isUrl('permissions') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
      <div :class="isUrl('permissions') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Permissions</div>
      </Link>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'

export default {
  components: {
    Icon,
    Link,
  },
  props: {
    auth: Object,
  },
  methods: {
    isUrl(...urls) {
      let currentUrl = this.$page.url.substr(1)
      if (urls[0] === '') {
        return currentUrl === ''
      }
      return urls.filter((url) => currentUrl.startsWith(url)).length
    },
  },
}
</script>
