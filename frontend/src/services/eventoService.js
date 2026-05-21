import { api } from 'boot/axios'

const BASE_URL = '/api/cte'

export const eventoService = {
  // Eventos obtengo los tipos de eventos que tenemos :)
  async getAllTiposEvento() {
    const response = await api.get(`http://localhost:8000/api/eve/tipos_evento`)
    //console.log(response)
    return response.data
  },
  //:)
  async getAllEventos() {
    const response = await api.get(`http://localhost:8000/api/eve/eventos`)
    //console.log(response)
    return response.data
  },
  //:)
  async getCategoriasByEvento(eventoId) {
    const response = await api.get(`http://localhost:8000/api/eve/categorias_ticket/${eventoId}`)
    return response.data
  },
   async createCompra(data){
    try{
      const response = await api.post(`http://localhost:8000/api/vta/factura/store`, data)
      return response.data
    }catch (error){
        console.log("ERROR BACKEND: " + error)
      throw error
    }

  },
  //:)
  async pasarelaPago(datosTarjeta){
    try{
      const response = await api.post(`http://localhost:8001/api/autorizar`, datosTarjeta)
      //return response.data
      if (response.data.message == "Transacción no autorizada: monto excede el límite."){
        return false
      }
      //console.log(response.status)
      return true
    }catch (error){
      console.log("ERROR BACKEND: " + error)
      throw error
        
    }

  },
  //:)
  async DetalleCompra(detalle){
    try{
      const response = await api.post(`http://localhost:8000/api/vta/facturaDetalle/store`, detalle)
      return response.data
    }catch (error){
      console.log("ERROR BACKEND: " + error)
      throw error
        
    }
  },  

  async VerficarDescuento(codigo, idEvento) {
    const response = await api.get(`http://localhost:8000/api/eve/descuentos/${codigo}/${idEvento}`)
    return response.data
  },

  async createEvento(evento) {
    const response = await api.post(`http://localhost:8000/api/eve/eventos`, evento)
    return response.data
  },

  async updateEvento(id, evento) {
    const response = await api.put(`http://localhost:8000/api/eve/eventos/${id}`, evento)
    return response.data
  },

  async deleteEvento(id) {
    await api.delete(`http://localhost:8000/api/eve/eventos/${id}`)
  },



  async createCategoria(categoria) {
    const response = await api.post(`${BASE_URL}/categorias-ticket`, categoria)
    return response.data
  },

  async updateCategoria(id, categoria) {
    const response = await api.put(`${BASE_URL}/categorias-ticket/${id}`, categoria)
    return response.data
  },

  async deleteCategoria(id) {
    await api.delete(`${BASE_URL}/categorias-ticket/${id}`)
  },

  async createTipoEvento(tipoEvento) {
    const response = await api.post(`http://localhost:8000/api/eve/tipos_evento`, tipoEvento)
    return response.data
  },

  async updateTipoEvento(id, tipoEvento) {
    const response = await api.put(`http://localhost:8000/api/eve/tipos_evento/${id}`, tipoEvento)
    return response.data
  },

  async deleteTipoEvento(id) {
    await api.delete(`http://localhost:8000/api/eve/tipos_evento/${id}`)
  },

  // Lugares de Evento
  async getAllLugares() {
    const response = await api.get(`http://localhost:8000/api/eve/lugares_evento`)
    return response.data
  },

  async createLugar(lugar) {
    const response = await api.post(`http://localhost:8000/api/eve/lugares_evento`, lugar)
    return response.data
  },

  async updateLugar(id, lugar) {
    const response = await api.put(`http://localhost:8000/api/eve/lugares_evento/${id}`, lugar)
    return response.data
  },

  async deleteLugar(id) {
    await api.delete(`http://localhost:8000/api/eve/lugares_evento/${id}`)
  },

  async getMetrics() {
    const response = await api.get(`http://localhost:8000/api/eve/eventos/dashboard/metrics`)
    return response.data
  },
}
