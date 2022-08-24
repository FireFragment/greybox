<template>
  <div>
    Register {{ role }}
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { Role } from 'src/types/role';

export default defineComponent({
  name: 'RegisterForm',

  components: {},

  computed: {
    role(): Role {
      // eslint-disable-next-line max-len
      // eslint-disable-next-line @typescript-eslint/no-unsafe-call,@typescript-eslint/no-unsafe-member-access
      return <Role> this.$store.getters['roles/roleFromSlug'](
        this.$route.params.role,
      );
    },
  },
});
</script>
