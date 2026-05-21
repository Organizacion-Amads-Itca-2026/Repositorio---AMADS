import { defineRouter } from '#q-app/wrappers'
import {
  createRouter,
  createMemoryHistory,
  createWebHistory,
  createWebHashHistory,
} from 'vue-router'
import routes from './routes'
import { authService } from 'src/services/authService'
import { Notify } from 'quasar'

/**
 * Función helper para verificar si el usuario tiene permisos
 */
function hasPermission(user, requiredPermissions) {
  if (!user || !user.permisos || !Array.isArray(user.permisos)) {
    return false
  }

  if (!requiredPermissions) {
    return true
  }

  if (typeof requiredPermissions === 'string') {
    // Un solo permiso requerido
    return user.permisos.some(p => p.nombre === requiredPermissions)
  }

  if (Array.isArray(requiredPermissions)) {
    // Verificar si el usuario tiene al menos uno de los permisos (OR logic)
    if (requiredPermissions.length === 0) return true
    return user.permisos.some(p =>
      requiredPermissions.includes(p.nombre)
    )
  }

  return false
}

export default defineRouter(function (/* { store, ssrContext } */) {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : process.env.VUE_ROUTER_MODE === 'history'
      ? createWebHistory
      : createWebHashHistory

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,
    history: createHistory(process.env.VUE_ROUTER_BASE),
  })

  // Navigation Guard
  Router.beforeEach((to, from, next) => {
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth)
    const isAuthenticated = authService.isAuthenticated()
    const user = authService.getUser()

    // Si la ruta requiere autenticación
    if (requiresAuth && !isAuthenticated) {
      Notify.create({
        type: 'negative',
        message: 'Debe iniciar sesión para acceder',
        position: 'bottom',
        timeout: 3000,
      })
      next('/')
      return
    }

    // Si está autenticado e intenta ir al login, redirigir al inicio
    if (to.path === '/' && isAuthenticated) {
      const role = authService.getUserRole()
      if (role === 'ADMIN' || role === 'ADMINISTRADOR') {
        next('/inicio')
      } else {
        next('/inicio-usuarios')
      }
      return
    }

    // Verificar roles si están definidos (mantenido para compatibilidad)
    if (requiresAuth && to.meta.roles) {
      const userRole = authService.getUserRole()
      if (!to.meta.roles.includes(userRole)) {
        Notify.create({
          type: 'negative',
          message: 'No tiene permisos para acceder a esta página',
          position: 'bottom',
          timeout: 3000,
        })
        next(from.path || '/inicio-usuarios')
        return
      }
    }

    // Verificar permisos específicos si están definidos en meta.requiredPermissions
    if (requiresAuth && to.meta.requiredPermissions) {
      if (!hasPermission(user, to.meta.requiredPermissions)) {
        Notify.create({
          type: 'negative',
          message: 'No tiene los permisos necesarios para acceder a esta página',
          position: 'bottom',
          timeout: 3000,
        })
        next(from.path || '/inicio-usuarios')
        return
      }
    }

    next()
  })

  Router.afterEach((to) => {
    const defaultTitle = 'VENTRY - Sistema de Tickets'
    document.title = to.meta.title || defaultTitle
  })

  return Router
})
