<template>
  <div class="dashboard">
    <h1>Dashboard</h1>

    <table class="table">
        <thead>
            <th>Username</th>
            <th>Email</th>
            <th>Roles</th>
            <th>Registration Date</th>
            <th>Last Logged In</th>
        </thead>

        <tbody>
            <tr v-for="user in users"
                :key="user.uuid"
            >
                <td>{{ user.username }}</td>
                <td>{{ user.email }}</td>
                <td>{{ user.is_admin }}</td>
                <td>{{ user.created_at }}</td>
                <td>{{ user.last_login }}</td>
            </tr>
        </tbody>
    </table>

  </div>
</template>


<style>

</style>


<script setup>

    import { Api } from '@/http.js'
    import { ref, onMounted } from 'vue'
    import { useRoute, useRouter } from 'vue-router'

    import { lsGetItem, lsSetItem } from '@/helpers.js'


    const Router = useRouter()
    const Route = useRoute()


    const user = ref(null)

    user.value = lsGetItem('user') || null
    const users = ref([])

    console.debug('users', users.value)


    onMounted(async function() {
        let res = await Api.get('users')

        users.value = res.data.users

    })



</script>
