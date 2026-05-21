import { api } from 'boot/axios'

const BASE_URL = '/api/vta'

export const descuentoService = {
  async getAllDescuentos() {
    const response = await api.get(`${BASE_URL}/descuentos`)
    return response.data
  },

  async getDescuentoById(id) {
    const response = await api.get(`${BASE_URL}/descuentos/${id}`)
    return response.data
  },

  async getDescuentoByCodigo(codigo) {
    const response = await api.get(`${BASE_URL}/descuentos/codigo/${codigo}`)
    return response.data
  },

  async getDescuentosByEstado(estado) {
    const response = await api.get(`${BASE_URL}/descuentos/estado/${estado}`)
    return response.data
  },

  async getDescuentosVigentes() {
    const response = await api.get(`${BASE_URL}/descuentos/vigentes`)
    return response.data
  },

  async getDescuentosDisponibles() {
    const response = await api.get(`${BASE_URL}/descuentos/disponibles`)
    return response.data
  },

  async createDescuento(descuento) {
    const response = await api.post(`${BASE_URL}/descuentos`, descuento)
    return response.data
  },

  async updateDescuento(id, descuento) {
    const response = await api.put(`${BASE_URL}/descuentos/${id}`, descuento)
    return response.data
  },

  async usarDescuento(id) {
    const response = await api.post(`${BASE_URL}/descuentos/${id}/usar`)
    return response.data
  },

  async deleteDescuento(id) {
    await api.delete(`${BASE_URL}/descuentos/${id}`)
  }
}
