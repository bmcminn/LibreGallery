<template>
  <main>

    <AuthComponent
        v-if="!state.user"
        show-login-view
    />





  </main>
</template>


<script setup>

import { useStorage } from '@vueuse/core'
import AuthComponent from '@/components/AuthComponent.vue'


const state = useStorage('app-state', { user: null, })


console.log(state)



</script>
