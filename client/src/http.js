import axios from 'axios'

export const Api = axios.create({
    baseURL: 'http://localhost:3005/api/',
    timeout: 6000,
    withCredentials: true,
})
