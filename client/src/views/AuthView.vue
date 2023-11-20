<template>
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
</template>


<style>

</style>


<script setup>

    import { Api } from '@/http.js'
    import { ref } from 'vue'
    // const testEmail = 'bob@law.blah'
    // const testPassword = 'testing123'

    const isLoginView           = window.location.href.includes(window.AppConfig.routes.login)
    const isRegistrationView    = window.location.href.includes(window.AppConfig.routes.register)

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

            if (!res.success) {
                errorMessage.value = res.message
            }

            console.log(res)

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
