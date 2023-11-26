import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [

        {
            path: '/',
            name: 'home',
            component: HomeView,
        },

        {
            path: '/dashboard',
            name: 'dashboard',
            // route level code-splitting
            // this generates a separate chunk (About.[hash].js) for this route
            // which is lazy-loaded when the route is visited.
            component: () => import('@/views/DashboardView.vue'),

            children: [
                {
                    path: 'users',
                    name: 'dashboardusers',
                    component: () => import('@/views/DashboardUsersView.vue'),
                },{
                    path: 'collections',
                    name: 'dashboardcollections',
                    component: () => import('@/views/DashboardCollectionsView.vue'),
                },
            ],
        },

        {
            path: '/login',
            name: 'login',
            component: () => import('@/views/AuthView.vue'),
            force: true,
        },

        {
            path: '/register',
            name: 'register',
            component: () => import('@/views/AuthView.vue'),
            force: true,
        },

        {
            path: '/forgot-password',
            name: 'forgotpassword',
            component: () => import('@/views/AuthView.vue'),
            force: true,
        },

    ]
})

export default router
