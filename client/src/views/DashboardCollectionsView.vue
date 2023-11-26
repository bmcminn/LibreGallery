<template>
  <div class="dashboard">


    <div>
        <!-- Total collections: {{ collections.length }} -->

        <button @click="toggleCreateForm">+New Collection</button>
    </div>


    <div v-if="showCreateForm">
        <FormKit
            type="form"
            submit-label="Create"
            @submit="handleCreate"
        >
            <FormKit type="text"
                name="collection_name"
                label="Collection name"
            />

            <FormKit type="file"
                name="files"
                multiple
            />
        </FormKit>
    </div>


    <DataGrid
        :entries="collections"
        listKey="uuid"
        gridName="collections-datagrid"
        listType="table"
    >
        <template v-slot:tableHeaders>
            <th>Filename</th>
            <th>Created</th>
        </template>

        <template v-slot:tableRow="slotProps">
            <td>{{ slotProps.entry.filename }}</td>
            <td>{{ slotProps.entry.created_at }}</td>
            <td>
                <button @click="editCollection(slotProps.entry)">Edit</button>
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


    const collections = ref([])


    const showCreateForm    = ref(false)
    const showEditForm      = ref(false)


    function toggleCreateForm() {
        showCreateForm.value = !showCreateForm.value
        console.log('toggle form', showCreateForm.value)
    }

    function toggleEditForm() {
        showEditForm.value != showEditForm.value
    }


    function handleCreate(e) {
        console.log(e)

        console.log('test hydration')
    }


    function editCollection(user) {
        console.log(user)
    }


    async function getCollections() {
        try {
            let res = await Api.get('collections')

            collections.value = res.data.collections
        } catch(err) {
            console.error(err)
        }
    }


    onMounted(async function() {
        collections.value = []
        await getCollections()
    })


</script>
