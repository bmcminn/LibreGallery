<template>
    <div class="dashboard">
        <h1>Dashboard</h1>

        <div class="flex">
            <nav>
                <ul>
                    <RouterLink :to="{ name: 'dashboardusers' }">Users</RouterLink>
                </ul>
            </nav>

            <RouterView></RouterView>
        </div>
    </div>
</template>


<style>

</style>


<script setup>

    import { Api } from '@/http.js'
    import { ref, onMounted } from 'vue'
    import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router'

    import { lsGetItem, lsSetItem } from '@/helpers.js'


    const Router = useRouter()
    const Route = useRoute()

    const user  = ref(null)

    user.value  = lsGetItem('user') || null


    onMounted(async function() {

    })


</script>
