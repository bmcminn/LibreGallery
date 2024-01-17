<template>
    <div>

        <div v-if="isLoginView"
            class="user-form login"
        >
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



        <div v-if="isRegistrationView"
            class="user-form register"
        >
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



        <div v-if="isPasswordResetRequestView"
            class="user-form password-reset password-reset-request"
        >
            <h1>Reset Password Request</h1>

            <FormKit
                type="form"
                submit-label="Request Password Reset"
                @submit="handlePasswordResetRequest"
            >
                <FormKit type="email"
                    name="email"
                    label="Email"
                    required
                />
            </FormKit>


            <p v-if="errorMessage"
                class="error-message"
            >
                {{ errorMessage }}
                <span v-if="errorTimer">{{ errorTimer }} seconds</span>
            </p>
            <!--
            <RouterLink :to="{name: 'login'}">
                Login
            </RouterLink>
             -->
        </div>


        <div v-if="isPasswordResetView"
            class="user-form password-reset"
        >
            <h1>Reset Password</h1>

            <FormKit
                type="form"
                submit-label="Reset Password"
                :disabled="!enableForm"
                @submit="handlePasswordReset"
            >

                <FormKit type="password"
                    name="password"
                    label="Password"
                    required
                />
                <FormKit type="password"
                    name="passwordconfirm"
                    label="Confirm Password"
                    required
                />
            </FormKit>


            <p v-if="errorMessage"
                class="error-message"
            >
                {{ errorMessage }}
                <span v-if="errorTimer">{{ errorTimer }} seconds</span>
            </p>
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
    import { ref, computed, defineProps } from 'vue'
    import { lsGetItem, lsSetItem } from '@/helpers.js'
    import { useRoute, useRouter } from 'vue-router'
    import { useUserStore } from '@/stores/user.js'
    import { isEmpty } from '@/helpers.js'

    // const testEmail = 'bob@law.blah'
    // const testPassword = 'testing123'

    const props = defineProps({
        showLoginView: Boolean,
        showRegistrationView: Boolean,
        showPasswordResetRequestView: Boolean,
        showPasswordResetView: Boolean,
    })

    const Router    = useRouter()
    const Route     = useRoute()

    const HREF          = window.location.href

    const PAGE_URL      = new URL(HREF)
    const QUERY_PARAMS  = new URLSearchParams(PAGE_URL.search)
    const Routes        = window.AppConfig.routes

    const isLoginView                   = props.showLoginView               || HREF.includes(Routes.login)
    const isRegistrationView            = props.showRegistrationView        || HREF.includes(Routes.register)
    const isPasswordResetRequestView    = props.showPasswordResetRequestView || HREF.includes(Routes.passwordreset) && !!!QUERY_PARAMS.get('token')
    const isPasswordResetView           = props.showPasswordResetView       || HREF.includes(Routes.passwordreset) && !!QUERY_PARAMS.get('token')

    const passwordResetToken = QUERY_PARAMS.get('token')

    const enableForm    = ref(true)
    const errorMessage  = ref('')
    const errorTimer    = ref(0)

    const userStore = useUserStore()

    const user = computed(() => userStore.user )


    if (isPasswordResetView && !AppConfig.isValidToken) {
        setError('Password reset token is invalid. Redirecting you to Password Reset Request page.')
        errorTimer.value = 5
        enableForm.value = false


        const timerInterval = setInterval(() => {
            errorTimer.value -= 1

            if (errorTimer.value === 0) {
                Router.push({ name: 'passwordreset' })
                clearInterval(timerInterval)
            }
        }, 1000)
    }



    function setError(err) {

        if (!err) {
            errorMessage.value = null;
        }

        console.error(err)
        errorMessage.value = err
    }


    async function handleLogin(e) {
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

        } catch(err) {
            setError(err)
        }
    }


    async function handleRegistration(e) {


    }



    async function handlePasswordResetRequest(e) {

        setError()

        let res

        try {

            const { email } = e

            res = await Api.post('auth/password-reset', {
                email,
            })

        } catch(err) {
            setError(err)
        }
    }


    async function handlePasswordReset(e) {

        setError()
        let res

        try {

            const { password, passwordconfirm } = e

            res = await Api.post('auth/verify-password-reset', {
                password,
                passwordconfirm,
                token: passwordResetToken,
            })

            // TODO: fix error messaging for invalid user password submissions
        } catch(err) {
            setError(err)
        }
    }

</script>
