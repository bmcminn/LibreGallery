import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'

import { lsGetItem, lsSetItem } from '@/helpers.js'


const STORE_LABEL = 'user_state'


export const useUserStore = defineStore('user', {

    state: () => {
        return {
            user: lsGetItem(STORE_LABEL),
        }
    },


    getters: {

        getUser(state) {
            return state.user
        },

        isLoggedIn(state) {
            return !!state.user
        },

    },


    actions: {

        update(user) {
            this.user = lsSetItem(STORE_LABEL, user)
        },

    },
})
