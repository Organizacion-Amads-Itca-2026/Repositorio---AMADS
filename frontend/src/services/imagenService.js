// src/services/imagenService.js
const BASE_IMAGE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8080'
const DEFAULT_ICON = '/assets/icono_evento.png' // o tu ruta de icono por defecto

export const imagenService = {
  /**
   * Devuelve la URL completa de una imagen del backend
   * @param {string|null} path - Ruta parcial de la imagen (por ejemplo: /uploads/eventos/foto.png)
   * @param {string} [fallback] - Imagen alternativa si no hay foto (por defecto usa DEFAULT_ICON)
   * @returns {string} URL lista para usar en <img :src="...">
   */
  getUrl(path, fallback = DEFAULT_ICON) {
    if (!path) return fallback

    // Si ya viene una URL absoluta (ej. http:// o https://), se devuelve tal cual
    if (path.startsWith('http://') || path.startsWith('https://')) {
      return path
    }

    // Si no empieza con '/', se agrega
    const normalizedPath = path.startsWith('/') ? path : `/${path}`

    return `${BASE_IMAGE_URL}${normalizedPath}`
  },

  /**
   * Devuelve la imagen local de fallback
   */
  getDefault() {
    return DEFAULT_ICON
  },
}
