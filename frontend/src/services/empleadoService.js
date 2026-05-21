import { api } from 'boot/axios'

const BASE_URL = '/api/adm/empleados' // La URL de tu EmpleadoController

export const empleadoService = {
  /**
   * Obtiene la lista de todos los empleados (DTO de respuesta).
   * Llama a: GET /api/adm/empleados
   */
  async getAllEmpleados() {
    const response = await api.get(BASE_URL)
    return response.data
  },

  /**
   * Obtiene el DTO de un empleado para el formulario de edición.
   * Llama a: GET /api/adm/empleados/dto/{id}
   */
  async getEmpleadoDTOById(id) {
    const response = await api.get(`${BASE_URL}/dto/${id}`)
    return response.data
  },

  /**
   * Crea un nuevo empleado.
   * Llama a: POST /api/adm/empleados
   * @param {EmpleadoDTO} empleadoDTO
   */
  async createEmpleado(empleadoDTO) {
    const response = await api.post(BASE_URL, empleadoDTO)
    return response.data
  },

  /**
   * Actualiza un empleado existente.
   * Llama a: PUT /api/adm/empleados/{id}
   * @param {number} id
   * @param {EmpleadoDTO} empleadoDTO
   */
  async updateEmpleado(id, empleadoDTO) {
    const response = await api.put(`${BASE_URL}/${id}`, empleadoDTO)
    return response.data
  },

  /**
   * Elimina un empleado por su ID.
   * Llama a: DELETE /api/adm/empleados/{id}
   * @param {number} id
   */
  async deleteEmpleado(id) {
    await api.delete(`${BASE_URL}/${id}`)
  },
}