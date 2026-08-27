import axios from 'axios'
import { storage } from '@/services/storage'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

api.interceptors.request.use((config) => {
  const token = storage.get('auth_token')

  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  
  return config
})

api.interceptors.response.use((response) => response, (error) => {
  if (error.response?.status === 401) {
      storage.remove('auth_user')
      storage.remove('auth_token')

      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default api