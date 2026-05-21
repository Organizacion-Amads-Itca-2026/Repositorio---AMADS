import { api } from 'boot/axios'

const BASE_URL = '/api/vta'

export const ticketService = {
  async getAllTickets() {
    const response = await api.get(`${BASE_URL}/tickets`)
    return response.data
  },

  async getTicketById(id) {
    const response = await api.get(`${BASE_URL}/tickets/${id}`)
    return response.data
  },

  async createTicket(ticket) {
    const response = await api.post(`${BASE_URL}/tickets`, ticket)
    return response.data
  },

  async updateTicket(id, ticket) {
    const response = await api.put(`${BASE_URL}/tickets/${id}`, ticket)
    return response.data
  },

  async deleteTicket(id) {
    await api.delete(`${BASE_URL}/tickets/${id}`)
  },

  async getTicketsByCompra(compraId) {
    const response = await api.get(`${BASE_URL}/tickets/compra/${compraId}`)
    return response.data
  },

  async getTicketsByCliente(clienteId) {
    const response = await api.get(`${BASE_URL}/tickets/cliente/${clienteId}`)
    return response.data
  },

  async validarTicket(id) {
    const response = await api.post(`${BASE_URL}/tickets/${id}/validar`)
    return response.data
  },

  async validarTicketPorNumero(numeroTicket) {
    const response = await api.post(`${BASE_URL}/tickets/validar/numero/${numeroTicket}`)
    return response.data
  },

  async getTicketsPorValidar() {
    const response = await api.get(`${BASE_URL}/tickets/por-validar`)
    return response.data
  },
}
