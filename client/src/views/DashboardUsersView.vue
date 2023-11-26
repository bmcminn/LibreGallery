<template>
  <div class="dashboard">


    <div>
        Total users: {{ users.length }}
    </div>


    <DataGrid
        :entries="users"
        listKey="uuid"
        gridName="users-datagrid"
        :showControls="false"
        listType="table"
    >
        <template v-slot:tableHeaders>
            <th>Username</th>
            <th>Email</th>
            <th>Roles</th>
            <th>Registration Date</th>
            <th>Last Logged In</th>
        </template>

        <template v-slot:tableRow="slotProps">
            <td>{{ slotProps.entry.username }}</td>
            <td>{{ slotProps.entry.email }}</td>
            <td class="text-center">{{ slotProps.entry.is_admin }}</td>
            <td>{{ slotProps.entry.created_at }}</td>
            <td>{{ slotProps.entry.last_login }}</td>
            <td>
                <button @click="editUser(slotProps.entry)">Edit</button>
            </td>
        </template>
    </DataGrid>

  </div>
</template>


<style>

</style>


<script setup>

    import { Api } from '@/http.js'
    import { ref, onMounted } from 'vue'

    import { lsGetItem, lsSetItem } from '@/helpers.js'

    import DataGrid from '@/components/DataGrid.vue'


    const users = ref([])

    console.debug('users', users.value)


    function editUser(user) {
        console.log(user)
    }


    onMounted(async function() {
        let res = await Api.get('users')

        users.value = res.data.users

    })


</script>
