<template>
    <div>
        <header>
            <div class="wrapper">

                <nav>
                    <RouterLink to="/">Home</RouterLink>

                    <RouterLink v-if="!user"
                        :to="{ name: 'login' }"
                    >Login</RouterLink>

                    <a v-if="user"
                        @click="logoutUser"
                    >Logout</a>

                    <RouterLink v-if="!user"
                        :to="{ name: 'register' }"
                    >Register</RouterLink>
                </nav>
            </div>
        </header>

        <RouterView :key="$route.fullPath"/>
    </div>
</template>


<style scoped>
    nav a.router-link-exact-active {
      color: var(--color-text);
    }

    nav a.router-link-exact-active:hover {
      background-color: transparent;
    }

    nav a {
      display: inline-block;
      padding: 0 1rem;
      border-left: 1px solid var(--color-border);
    }

    nav a:first-of-type {
      border: 0;
    }

</style>


<script setup>
import { RouterLink, RouterView } from 'vue-router'

import { Api } from '@/http.js'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { lsGetItem, lsSetItem } from '@/helpers.js'

import { useUserStore } from '@/stores/user.js'


const Router = useRouter()

// TODO: convert to pinia instance; make pinia reference localStorage
const userStore = useUserStore()

const user = computed(() => userStore.getUser)


async function logoutUser() {
    try {
        let res = await Api.post('auth/logout')
        userStore.update(null)
        Router.push({ name: 'home' })

    } catch(err) {
        console.error(err)
    }
}

</script>
