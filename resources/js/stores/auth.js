import { defineStore } from 'pinia'
import { authApi } from '@/api/auth'
import { storage } from '@/services/storage'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: storage.get('auth_user', null),
    token: storage.get('auth_token', null),
    loading: false
  }),
  getters: {
    isAuthenticated: (state) => !!state.token
  },
  actions: {
    async login(credentials) {
      this.loading = true
      try {
        const { user, token } = await authApi.login(credentials)
        this.setSession(user, token, credentials.remember)
        return user
      } finally {
        this.loading = false
      }
    },
    async register(payload) {
      this.loading = true
      try {
        const { user, token } = await authApi.register(payload)
        this.setSession(user, token, true)
        return user
      } finally {
        this.loading = false
      }
    },
    setSession(user, token, remember = true) {
      this.user = user
      this.token = token
      if (remember) {
        storage.set('auth_user', user)
        storage.set('auth_token', token)
      }
    },
    updateProfile(payload) {
      this.user = { ...this.user, ...payload }
      storage.set('auth_user', this.user)
    },
    logout() {
      this.user = null
      this.token = null
      storage.remove('auth_user')
      storage.remove('auth_token')
    }
  }
})
