
<template>
    <div>

        <div v-if="showDebug">
            <h2>DEBUG:</h2>
            <ul>
                <li>isTable: {{ isTable }}</li>
                <li>isGrid: {{ isGrid }}</li>
            </ul>

        </div>


        <div v-if="showControls"
            class="datagrid-controls flex space-between align-center"
        >
            <div></div>
            <div class="align-end">
                <button @click="toggleTableMode"
                    :class="{
                        active: isGrid,
                    }"
                    >Grid</button>
                <button @click="toggleTableMode"
                    :class="{
                        active: isTable,
                    }"
                >List</button>
            </div>
        </div>


        <ul class="datagrid-grid" v-if="isGrid">
            <li v-for="entry in entries"
                :key="entry[listKey]"
            >
                <slot name="listEntry"
                    :entry="entry"
                >
                    <td>{{ entry }}</td>
                </slot>
            </li>
        </ul>


        <table class="datagrid-table table" v-if="isTable">
            <thead>
                <slot name="tableHeaders">
                    <th>Sample Header</th>
                </slot>
            </thead>
            <tbody>
                <tr v-for="entry in entries"
                    :key="entry[listKey]"
                >
                    <slot name="tableRow"
                        :entry="entry"
                    >
                        <td>{{ entry }}</td>
                    </slot>
                </tr>
                <tr v-if="entries.length === 0">
                    <td colspan="40" class="text-center">
                        No results available
                    </td>
                </tr>
            </tbody>
        </table>

        <nav>

        </nav>
    </div>
</template>


<style scoped>

    .datagrid-controls button.active {
        color: #f90;
    }

</style>



<script setup>
import { ref, computed } from 'vue'
import { lsGetItem, lsSetItem } from '@/helpers.js'



const MODE_TABLE = 'table'
const MODE_GRID = 'grid'

const props = defineProps({
    entries: Array,

    gridName: String,

    listKey: String,
    listType: String,

    showDebug: {
        type: Boolean,
        default: false,
    },
    showControls: {
        type: Boolean,
        default: true,
    },
})

const listType = ref(lsGetItem(props.gridName) ?? props.listType ?? MODE_TABLE)


const isTable = computed(() => listType.value === MODE_TABLE)
const isGrid = computed(() => listType.value === MODE_GRID)



function toggleTableMode() {
    listType.value = listType.value === MODE_TABLE ? MODE_GRID : MODE_TABLE
    lsSetItem(props.gridName, listType.value)
}


</script>
