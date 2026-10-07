// ============================================================================
// WhatsApp API service (SEMENTARA)
// ----------------------------------------------------------------------------
// Implementasi sementara menggunakan localStorage + respons dummy agar halaman
// bisa langsung dipakai. Ganti isi tiap fungsi dengan panggilan axios asli
// ke backend ketika endpoint sudah tersedia, misalnya:
//
//   import api from '@/api/api'
//   list:     () => api.get('/api/v1/whatsapp')
//   store:    (payload) => api.post('/api/v1/whatsapp', payload)
//   update:   (id, payload) => api.put(`/api/v1/whatsapp/${id}`, payload)
//   destroy:  (id) => api.delete(`/api/v1/whatsapp/${id}`)
//
// Halaman memakai struktur respons `{ data: ... }` agar mirip Laravel.
// ============================================================================

import { storage } from './storage'
import api from '@/api/api'

const STORAGE_KEY = 'whatsapp_nomor'

const WAIT_MS = 350

const delay = (data) =>
  new Promise((resolve) =>
    setTimeout(() => resolve({ data: JSON.parse(JSON.stringify(data)) }), WAIT_MS)
  )

const seed = () => [
  {
    id: 1,
    nama: 'Bendahara Sekolah',
    nomor: '081234567890',
    aktif: true,
    created_at: new Date().toISOString(),
  },
  {
    id: 2,
    nama: 'Kepala Sekolah',
    nomor: '081298765432',
    aktif: true,
    created_at: new Date().toISOString(),
  },
]

const readAll = () => {
  const raw = storage.get(STORAGE_KEY, null)
  if (raw) return raw
  const initial = seed()
  storage.set(STORAGE_KEY, initial)
  return initial
}

const writeAll = (items) => storage.set(STORAGE_KEY, items)

const list = async (params = {}) => {
  const response = await api.get('/whatsapp-sessions')
  return delay(response)
}

const getQr = async (id) => {
  const response = await api.get(`/whatsapp-generate-qr/${id}`)
  return delay(response)
}

const createSession = async (params = {}) => {
  const response = await api.post('/whatsapp-session')
  return delay(response)
}

const show = (id) => {
  const item = readAll().find((it) => it.id === Number(id))
  if (!item) return Promise.reject(new Error('Data tidak ditemukan'))
  return delay(item)
}

const store = (payload) => {
  const items = readAll()
  const newItem = {
    id: items.length ? Math.max(...items.map((i) => i.id)) + 1 : 1,
    nama: payload.nama,
    nomor: payload.nomor,
    aktif: payload.aktif ?? true,
    created_at: new Date().toISOString(),
  }
  writeAll([...items, newItem])
  return delay(newItem)
}

const update = (id, payload) => {
  const items = readAll()
  const idx = items.findIndex((it) => it.id === Number(id))
  if (idx === -1) return Promise.reject(new Error('Data tidak ditemukan'))

  const updated = { ...items[idx], ...payload, id: items[idx].id }
  items[idx] = updated
  writeAll(items)
  return delay(updated)
}

const destroy = (id) => {
  const items = readAll().filter((it) => it.id !== Number(id))
  writeAll(items)
  return delay(true)
}

const qrcode = async () => {
  try {
    const response = await api.get('/siswa-index')
  } catch (error) {
    console.error('[In-Memory Search] Gagal memuat data master siswa:', error)
    isError.value = true
  } finally {
    isLoading.value = false
  }
}

export const whatsappApi = {
  list,
  show,
  store,
  update,
  destroy,
  qrcode,
  getQr,
  createSession
}
