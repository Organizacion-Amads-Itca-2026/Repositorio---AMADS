import { api } from 'boot/axios'

const BASE_URL = '/api/prv'

export const proveedorService = {
  async getAllProveedores() {
    const response = await api.get(`${BASE_URL}/proveedores`)
    //console.log(response)
    return response.data
  },

  async getProveedorById(id) {
    const response = await api.get(`${BASE_URL}/proveedores/${id}`)
    return response.data
  },

  async createProveedor(proveedor) {
    const response = await api.post(`${BASE_URL}/proveedores`, proveedor)
    return response.data
  },

  async updateProveedor(id, proveedor) {
    const response = await api.put(`${BASE_URL}/proveedores/${id}`, proveedor)
    return response.data
  },

  async deleteProveedor(id) {
    await api.delete(`${BASE_URL}/proveedores/${id}`)
  },

  async getProveedoresByEstado(estado) {
    const response = await api.get(`${BASE_URL}/proveedores/estado/${estado}`)
    return response.data
  },
}
