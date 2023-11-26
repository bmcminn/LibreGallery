import { defineStore } from 'pinia'

import { lsGetItem, lsSetItem } from '@/helpers.js'


const STORE_LABEL = 'user'


export const useUserStore = defineStore('counter', {

    state: () => {
        return {
            user: lsGetItem(STORE_LABEL),

        }
    },


    getters: {

        getUser(state) {
            return state.user
        },

    },


    actions: {

        update(user) {
            this.user = lsSetItem(STORE_LABEL, user)
        },

    },
})
