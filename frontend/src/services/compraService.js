import { api } from 'boot/axios'

const BASE_URL = '/api/vta'

export const compraService = {
  async getAllCompras() {
    const response = await api.get(`${BASE_URL}/compras`)
    return response.data
  },

  async getCompraById(id) {
    const response = await api.get(`${BASE_URL}/compras/${id}`)
    return response.data
  },

  async createCompra(compra) {
    const response = await api.post(`${BASE_URL}/compras`, compra)
    return response.data
  },

  async updateCompra(id, compra) {
    const response = await api.put(`${BASE_URL}/compras/${id}`, compra)
    return response.data
  },

  async deleteCompra(id) {
    await api.delete(`${BASE_URL}/compras/${id}`)
  },

  async getComprasByCliente(clienteId) {
    const response = await api.get(`${BASE_URL}/compras/cliente/${clienteId}`)
    return response.data
  },

  async getComprasByEvento(eventoId) {
    const response = await api.get(`${BASE_URL}/compras/evento/${eventoId}`)
    return response.data
  },
}
