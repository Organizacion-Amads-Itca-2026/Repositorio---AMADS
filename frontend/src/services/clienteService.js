import { api } from 'boot/axios'

const BASE_URL = '/api/vta'
///api/vta/clientes

export const clienteService = {
  async getAllClientes() {
    const response = await api.get(`${BASE_URL}/clientes`)
    return response.data
  },

  async getClienteById(id) {
    const response = await api.get(`${BASE_URL}/clientes/${id}`)
    return response.data
  },

  async createCliente(cliente) {
    const response = await api.post(`${BASE_URL}/clientes`, cliente)
    return response.data
  },

  async updateCliente(id, cliente) {
    const response = await api.put(`${BASE_URL}/clientes/${id}`, cliente)
    return response.data
  },

  async deleteCliente(id) {
    await api.delete(`${BASE_URL}/clientes/${id}`)
  },

  async getClientesByEstado(estado) {
    const response = await api.get(`${BASE_URL}/clientes/estado/${estado}`)
    return response.data
  },
}
