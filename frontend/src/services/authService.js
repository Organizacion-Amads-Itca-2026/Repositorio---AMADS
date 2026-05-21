import { api } from 'boot/axios'

const TOKEN_KEY = 'auth_token'
const USER_KEY = 'auth_user'

export const authService = {
  // Login
  async login(email, password) {

    try {
      const response = await api.post('http://127.0.0.1:8000/api/seg/login', { email, password })

      if (response.data.access_token) {
        this.setToken(response.data.access_token)

        const userFromDB = response.data.user

        // Armamos el usuario con la información que viene de Laravel
        const userData = {
          email: userFromDB.email,
          nombres: userFromDB.nombres || userFromDB.name,
          apellidos: userFromDB.apellidos || '',
          // Aquí guardamos el texto del perfil (ej. "ADMINISTRADOR" o "USUARIO")
          perfil: userFromDB.perfil
        }
        this.setUser(userData)
      }
      console.log(response.data)
      return response.data
    } catch (error) {
      console.log('ENTRÓ AL CATCH')
      console.error(error)
    }
  },

  //  // Registro
  //async register(usuario) {
  //const response = await api.post('/api/seg/register', usuario)
  //return response.data
  //},

  // Registro
  async register(usuario) {
    const response = await api.post('http://127.0.0.1:8000/api/seg/register', usuario)
    return response.data
  },

  // Logout
  //logout() {
  //localStorage.removeItem(TOKEN_KEY)
  //localStorage.removeItem(USER_KEY)
  //},

  // Logout
  async logout() {
    const token = this.getToken()

    // Si hay un token, le avisamos a Laravel que lo destruya
    if (token) {
      try {
        await api.post('http://127.0.0.1:8000/api/seg/logout', {}, {
          headers: {
            Authorization: `Bearer ${token}` // Le enviamos el token para que sepa cuál borrar
          }
        })
      } catch (error) {
        console.error('Error al destruir el token en el servidor:', error)
      }
    }
    localStorage.removeItem(TOKEN_KEY)
    localStorage.removeItem(USER_KEY)
  },

  // Guardar token
  setToken(token) {
    localStorage.setItem(TOKEN_KEY, token)
  },

  // Obtener token
  getToken() {
    return localStorage.getItem(TOKEN_KEY)
  },

  // Guardar usuario
  setUser(user) {
    localStorage.setItem(USER_KEY, JSON.stringify(user))
  },

  // Obtener usuario
  getUser() {
    const user = localStorage.getItem(USER_KEY)
    return user ? JSON.parse(user) : null
  },

  // Verificar si está autenticado
  isAuthenticated() {
    const token = this.getToken()

    // Si no hay token guardado, no está logueado
    if (!token) return false

    // Laravel Sanctum validará la sesión real en el backend, el frontend solo necesita saber que hay token
    return true
  },

  // Obtener rol/perfil del usuario
  getUserRole() {
    const user = this.getUser()
    // Lee directamente el campo "perfil" que guardamos en la función login
    return user?.perfil?.nombre || user?.perfil || null
  },

  // Verificar si tiene un rol específico
  hasRole(roleName) {
    return this.getUserRole() === roleName
  },
}