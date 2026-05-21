import { onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { authService } from 'src/services/authService'
import { Notify } from 'quasar' // Opcional, para avisarle al usuario

export function useIdleTimeout(timeoutMinutes = 15) {
  const router = useRouter()
  let timeoutId = null

  // Función que se ejecuta si se acaba el tiempo
  const logoutUser = async () => {
    if (authService.isAuthenticated()) {
      // Usamos el logout 
      await authService.logout() 
      
      // Lo mandamos al login
      router.push('/login') 
      
      // Le mostramos un mensaje 
      Notify.create({
        type: 'warning',
        message: 'La sesión ha expirado por inactividad.',
        position: 'top'
      })
    }
  }

  // Reiniciar el cronómetro
  const resetTimer = () => {
    if (timeoutId) clearTimeout(timeoutId)
    // Convertimos los minutos a milisegundos
    timeoutId = setTimeout(logoutUser, timeoutMinutes * 60 * 1000)
  }

  // Escuchar si el usuario está vivo
  const setupListeners = () => {
    window.addEventListener('mousemove', resetTimer)
    window.addEventListener('keydown', resetTimer)
    window.addEventListener('click', resetTimer)
    window.addEventListener('scroll', resetTimer)
  }

  // Limpiar la memoria si sale de la pantalla
  const removeListeners = () => {
    window.removeEventListener('mousemove', resetTimer)
    window.removeEventListener('keydown', resetTimer)
    window.removeEventListener('click', resetTimer)
    window.removeEventListener('scroll', resetTimer)
  }

  onMounted(() => {
    setupListeners()
    resetTimer() // Arrancar el reloj al cargar
  })

  onUnmounted(() => {
    removeListeners()
    if (timeoutId) clearTimeout(timeoutId)
  })
}