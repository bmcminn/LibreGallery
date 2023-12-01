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

            <!-- // TODO: add support for forgot password -->
            <RouterLink :to="{name: 'passwordreset'}">
                Forgot password?
            </RouterLink>
        </div>



        <div class="user-form register" v-if="isRegistrationView">
            <h1>Register</h1>

            <FormKit
                type="form"
                submit-label="Register"
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

            <RouterLink :to="{name: 'login'}">
                Login
            </RouterLink>

            <RouterLink :to="{name: 'passwordreset'}">
                Forgot password?
            </RouterLink>
        </div>



        <div class="user-form password-reset" v-if="isPasswordResetView">
            <h1>Reset Password</h1>

            <FormKit
                type="form"
                submit-label="Reset Password"
                @submit="handlePasswordReset"
            >
                <FormKit type="email"
                    name="email"
                    label="Email"
                    required
                />
            </FormKit>

            <!--
            <RouterLink :to="{name: 'login'}">
                Login
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
    const isPasswordResetView   = window.location.href.includes(window.AppConfig.routes.passwordreset)


    const userStore = useUserStore()

    const user = computed(() => userStore.user )


    let errorMessage = ref('')


    function setError(err) {

        if (!err) {
            errorMessage.value = null;
        }

        console.error(err)
        errorMessage.value = err
    }


    async function handleLogin(e) {
        console.log(e)

        setError()

        const { email, password } = e

        let res

        try {
            res = await Api.post('auth/login', {
                email, password
            })

            if (!res.data.success) {
                setError(res.message)
                return
            }

            console.log('user logged in', res.data.user)

            userStore.update(res.data.user)

            Router.push({ name: 'dashboard' })

        } catch(err) {
            setError(err)
        }
    }


    async function queryApi() {
        let res
        setError()

        try {
            res = await Api.get('users')

            console.log(res)

        } catch(err) {
            setError(err)
        }
    }


    async function logout() {
        let res

        setError()

        try {
            res = await Api.get('auth/logout')

            console.log(res)

        } catch(err) {
            setError(err)
        }
    }


    async function handleRegistration(e) {


    }



    async function handlePasswordReset(e) {

        setError()

        let res

        try {

            const { email } = e

            res = await Api.post('auth/password-reset', {
                email,
            })

            console.log(res)

        } catch(err) {
            setError(err)
        }
    }

</script>
