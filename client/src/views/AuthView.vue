<template>
    <div>

        <div class="user-form login" v-if="isLoginView">
            <h1>Login</h1>


            <FormKit
                type="form"
                submit-label="Login"
                @submit="handleLogin"
            >
                <FormKit type="email"
                    name="email"
                    label="Email"
                    required
                />
                    <!-- :value="testEmail" -->

                <FormKit type="password"
                    label="Password"
                    name="password"
                    required
                />
                    <!-- :value="testPassword" -->
            </FormKit>

            <p v-if="errorMessage">
                {{ errorMessage }}
            </p>


            <button @click="queryApi">Query API</button>
            <button @click="logout">Logout</button>

            <!--
            // TODO: add support for forgot password
            <RouterLink :to="{path: 'forgotpassword'}">
                Forgot password?
            </RouterLink>
             -->
        </div>



        <div class="user-form register" v-if="isRegistrationView">
            <h1>Register</h1>


            <FormKit
                type="form"
                @submit="handleRegistration"
            >
                <FormKit type="email"
                    name="email"
                    label="Email"
                    required
                />
                    <!-- :value="testEmail" -->

                <FormKit type="password"
                    label="Password"
                    name="password"
                    required
                />
                    <!-- :value="testPassword" -->
            </FormKit>

            <!--
            <RouterLink :to="{path: 'forgotpassword'}">
                Forgot password?
            </RouterLink>
             -->
        </div>
    </div>
</template>


<style>

</style>


<script setup>

    import { Api } from '@/http.js'
    import { ref, computed } from 'vue'
    import { lsGetItem, lsSetItem } from '@/helpers.js'
    import { useRoute, useRouter } from 'vue-router'
    import { useUserStore } from '@/stores/user.js'

    // const testEmail = 'bob@law.blah'
    // const testPassword = 'testing123'

    const Router    = useRouter()
    const Route     = useRoute()

    const isLoginView           = window.location.href.includes(window.AppConfig.routes.login)
    const isRegistrationView    = window.location.href.includes(window.AppConfig.routes.register)


    const userStore = useUserStore()


    const user = computed(() => userStore.user )


    let errorMessage = ref('')

    async function handleLogin(e) {
        console.log(e)

        errorMessage.value = null

        const { email, password } = e

        let res

        try {
            res = await Api.post('auth/login', {
                email, password
            })

            if (!res.data.success) {
                errorMessage.value = res.message
                return
            }

            console.log('user logged in', res.data.user)

            userStore.update(res.data.user)

            Router.push({ name: 'dashboard' })

        } catch(err) {
            console.error(err)
        }
    }


    async function queryApi() {
        let res

        try {
            res = await Api.get('users')

            console.log(res)

        } catch(err) {
            console.error(err)
        }
    }


    async function logout() {
        let res

        try {
            res = await Api.get('auth/logout')

            console.log(res)

        } catch(err) {
            console.error(err)
        }
    }


    async function handleRegistration(e) {


    }

</script>
