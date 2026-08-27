import api from './api'

export const authApi = {
  async login({ email, password }) {
    try {
      const response = await api.post('/login', {
        email,
        password,
      })

      return response.data
    } catch (error) {
      const message =
        error.response?.data?.message ||
        'Email atau password salah'

      throw {
        message,
        status: error.response?.status,
      }
    }
  },
  async register({ name, email }) {
    await wait()
    const user = { id: Date.now(), name, email, role: 'Admin', avatar: 'https://i.pravatar.cc/100?img=5' }
    const token = 'demo-token-' + Date.now()
    return { user, token }
  },
  async forgotPassword(email) {
    await wait()
    return { message: `Password reset instructions sent to ${email}` }
  }
}