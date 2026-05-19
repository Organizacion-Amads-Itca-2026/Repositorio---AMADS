import { api } from 'boot/axios'

const BASE_URL = '/api/seg/usuarios'

export const usuarioService = {
  // Obtener todos los usuarios
  async getAllUsuarios() {
    const response = await api.get(BASE_URL)
    return response.data
  },

  // Obtener usuario por ID
  async getUsuarioById(id) {
    const response = await api.get(`${BASE_URL}/${id}`)
    return response.data
  },

  // Obtener usuario por email
  async getUsuarioByEmail(email) {
    const response = await api.get(`${BASE_URL}/email/${email}`)
    return response.data
  },

  // Crear usuario (Llama al endpoint de admin)
  async createUsuario(usuarioDTO) {
    // Llama al endpoint de UsuarioController (/api/seg/usuarios)
    const response = await api.post(BASE_URL, usuarioDTO)
    return response.data
  },

  // Actualizar usuario
  async updateUsuario(id, usuario) {
    const response = await api.put(`${BASE_URL}/${id}`, usuario)
    return response.data
  },

  // Eliminar usuario
  async deleteUsuario(id) {
    await api.delete(`${BASE_URL}/${id}`)
  },

  // Obtener usuarios por estado
  async getUsuariosByEstado(estado) {
    const response = await api.get(`${BASE_URL}/estado/${estado}`)
    return response.data
  },

  // Cambiar contraseña (usa el authService)
  async cambiarPassword(data) {
    const response = await api.post('/api/auth/change-password', data)
    return response.data
  },
}
