<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { authService } from 'src/services/authService'

const router = useRouter()
const $q = useQuasar()

onMounted(() => {
  // Limpiar autenticación
  authService.logout()

  // Notificar al usuario
  $q.notify({
    type: 'info',
    message: 'Sesión cerrada correctamente',
    position: 'top',
    timeout: 2000,
    icon: 'logout'
  })

  // Redirigir al login
  setTimeout(() => {
    router.push('/')
  }, 500)
})
</script>

<template>
  <div class="fixed-center text-center">
    <q-spinner-hourglass size="80px" color="primary" />
    <div class="text-h6 q-mt-md">Cerrando sesión...</div>
  </div>
</template>
