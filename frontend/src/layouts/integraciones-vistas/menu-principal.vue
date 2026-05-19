<template>
  <div>
    <q-layout view="hHh Lpr lff" class="shadow-2 full-height" v-if="!isLoginPage">
      <q-header elevated :class="$q.dark.isActive ? 'bg-secondary' : 'bg-secondary'">
        <q-toolbar>
          <q-btn flat round dense icon="menu" @click="drawer = !drawer" />
          <q-toolbar-title class="row items-center">
            <img
              :src="logo_front"
              alt="Logo"
              class="q-mr-sm logo-img"
              style="max-height: 40px; width: auto"
              fit="contain"
            />
            <div class="status-badge q-ml-sm">
              {{ ambienteUsuario }}
            </div>
          </q-toolbar-title>
          <q-btn color="red" text-color="white" label="Cerrar Sesión" @click="cerrarSesion" />
        </q-toolbar>
      </q-header>

      <q-drawer
        v-model="drawer"
        show-if-above
        :breakpoint="500"
        bordered
        :class="$q.dark.isActive ? 'bg-grey-9' : 'bg-grey-3'"
      >
        <div
          class="column"
          style="
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
          "
        >
          <div>
            <q-item clickable class="q-my-md" style="cursor: default">
              <q-item-section avatar>
                <q-avatar size="56px">
                  </q-avatar>
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-weight-bold">{{ datos.nombre }}</q-item-label>
                <q-item-label caption>{{ perfilNombre }}</q-item-label>
                <q-item-label caption>{{ entidadUsuario }}</q-item-label>
              </q-item-section>
            </q-item>

            <q-list>
              <q-item-label header>Menu Principal</q-item-label>
            </q-list>
            <groupedLinks :links="linksList" />
          </div>
        </div>
      </q-drawer>

      <q-page-container>
        <q-page padding>
          <router-view />
        </q-page>
      </q-page-container>
      <q-footer class="bg-grey-3 text-black text-center bg-secondary" height-hint="50">
        <div class="q-pa-sm text-caption">
          V1.08.05 © {{ new Date().getFullYear() }} DMSYSTEMSV. Todos los derechos reservados.
        </div>
      </q-footer>
    </q-layout>
    <router-view v-else />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useQuasar } from 'quasar'
import { useRouter } from 'vue-router'
import groupedLinks from '../../components/componentes-generales/control-de-links-agrupados.vue'
import { authService } from 'src/services/authService'
import { useMenuRoutes } from 'src/composables/useMenuRoutes'

// 1. IMPORTAMOS EL DETECTOR DE INACTIVIDAD
import { useIdleTimeout } from 'src/composables/useIdleTimeout'

const router = useRouter()
const $q = useQuasar()

// 2. ACTIVAMOS EL DETECTOR (15 minutos de inactividad antes de sacarlo)
useIdleTimeout(15)

// Obtener usuario y menú dinámico
const { user, menuLinks } = useMenuRoutes()
import logo_front from '/Ventry.png'
const isLoginPage = computed(() => router.currentRoute.value.path === '/')

// Datos del usuario desde authService
const datos = computed(() => {
  const userData = user.value
  return {
    nombre: userData ? `${userData.nombres} ${userData.apellidos}` : 'Usuario',
    perfil: userData?.perfil || 'Sin perfil',
    entidad: 'VENTRY',
  }
})

const perfilNombre = computed(() => {
  const perfil = datos.value.perfil
  return perfil === 'ADMINISTRADOR' ? 'Administrador' : perfil
})

const entidadUsuario = computed(() => datos.value.entidad)

// Variables reactivas
const drawer = ref(false)
const ambienteUsuario = ref('DESARROLLO') // Cambiar según el ambiente

// Links del menú obtenidos dinámicamente
const linksList = computed(() => menuLinks.value)

// Función para cerrar sesión (Manual y Automática)
async function cerrarSesion() {
  try {
    // 3. AÑADIMOS 'await' PARA ASEGURAR QUE SE BORRE EL TOKEN EN LARAVEL PRIMERO
    await authService.logout()

    // Borrar cookies residuales
    const opciones = [
      'tokenFirmado=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;',
      'tokenFirmado=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; SameSite=Strict;',
      'tokenFirmado=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' +
        location.hostname +
        ';',
    ]

    opciones.forEach((cookie) => (document.cookie = cookie))

    // Redirige al login y muestra notificación
    router.push('/')
    $q.notify({ color: 'positive', message: 'Sesión cerrada exitosamente', icon: 'logout' })
  } catch (error) {
    console.error('Error al cerrar sesión:', error)
    $q.notify({ color: 'negative', message: 'Error al cerrar sesión', icon: 'error' })
  }
}
</script>

<style lang="scss">
.small-image {
  width: 5%;
}

@media screen and (max-width: 600px) {
  .logo-img {
    margin-left: auto;
    display: none;
  }

  .text-h4 {
    font-size: 20px !important;
    line-height: 1 !important;
  }
}

.loading-fullscreen-white {
  background-color: white !important;
}

.loading-custom-content {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.status-badge {
  padding: 10px 10px;
  border: 1px solid #585858;
  opacity: 0.7;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  font-size: 0.75rem;
  background-color: white;
  color: #333;
  white-space: nowrap;
}
</style>