import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { authService } from 'src/services/authService'

/**
 * Composable para obtener rutas del menú filtradas por rol del usuario
 */
export function useMenuRoutes() {
  const router = useRouter()

  // Obtener el usuario autenticado y su rol
  const user = computed(() => authService.getUser())
  const userRole = computed(() => {
    const userData = user.value
    if (!userData) return null

    // El perfil viene como String directamente del backend
    return userData.perfil || null
  })

  // Definir iconos por categoría
  const categoryIcons = {
    'Inicio': 'home',
    'Comercial': 'store',
    'Configuración': 'settings',
    'Seguridad': 'security',
    'Soporte': 'help'
  }

  // Definir iconos por ruta
  const routeIcons = {
    '/inicio': 'dashboard',
    '/inicio-usuarios': 'people',
    '/eventos': 'event',
    '/proveedores': 'business',
    '/clientes': 'person',
    '/punto-venta': 'point_of_sale',
    '/validar-tickets': 'verified',
    '/descuentos': 'local_offer',
    '/categorias-tickets': 'category',
    '/reportes': 'assessment',
    '/lugares-evento': 'place',
    '/tipos-evento': 'event_note',
    '/administrar-empleados': 'badge', // <--- CAMBIO AQUÍ (Añadido icono para empleados)
    '/auditoria-cambios': 'history',
    '/cambiar-clave': 'vpn_key',
    '/crear-usuario': 'person_add',
    '/administrar-usuarios': 'manage_accounts',
    '/manual-usuario': 'menu_book'
  }

  // Obtener todas las rutas del router
  const allRoutes = computed(() => {
    return router.getRoutes().filter(route => {
      // Filtrar solo rutas con meta.requiresAuth y que tengan title
      return route.meta?.requiresAuth && route.meta?.title && route.path !== '/cerrar-sesion'
    })
  })

  // Filtrar rutas por rol del usuario
  const filteredRoutes = computed(() => {
    if (!userRole.value) return []

    return allRoutes.value.filter(route => {
      const roles = route.meta?.roles

      // Si no tiene roles definidos, está disponible para todos los usuarios autenticados
      if (!roles || roles.length === 0) return true

      // Convertimos todo a mayúsculas para evitar errores de tipeo
      const userRoleUpper = String(userRole.value).toUpperCase()
      const rolesUpper = roles.map(r => String(r).toUpperCase())
      
      // Verificar si el rol del usuario está en la lista de roles permitidos
      return rolesUpper.includes(userRoleUpper)
    })
  })

  // Agrupar rutas por categoría
  const groupedRoutes = computed(() => {
    const groups = {
      'Inicio': [],
      'Comercial': [],
      'Configuración': [],
      'Seguridad': [],
      'Soporte': []
    }

    filteredRoutes.value.forEach(route => {
      const path = route.path

      // Determinar la categoría basada en el path
      if (path.includes('/inicio')) {
        groups['Inicio'].push(route)
      } else if (
        path.includes('/eventos') ||
        path.includes('/proveedores') ||
        path.includes('/clientes') ||
        path.includes('/punto-venta') ||
        path.includes('/validar-tickets') ||
        path.includes('/descuentos') ||
        path.includes('/categorias-tickets') ||
        path.includes('/reportes')
      ) {
        groups['Comercial'].push(route)
      } else if (
        path.includes('/lugares-evento') ||
        path.includes('/tipos-evento') ||
        path.includes('/administrar-empleados') // <--- CAMBIO AQUÍ (Añadida la ruta a este grupo)
      ) {
        groups['Configuración'].push(route)
      } else if (
        path.includes('/auditoria') ||
        path.includes('/cambiar-clave') ||
        path.includes('/crear-usuario') ||
        path.includes('/administrar-usuarios')
      ) {
        groups['Seguridad'].push(route)
      } else if (path.includes('/manual-usuario')) {
        groups['Soporte'].push(route)
      }
    })

    // Filtrar grupos vacíos
    return Object.entries(groups)
      .filter(([, routes]) => routes.length > 0)
      .reduce((acc, [key, value]) => {
        acc[key] = value
        return acc
      }, {})
  })

  // Convertir a formato compatible con control-de-links-agrupados
  const menuLinks = computed(() => {
    const links = []

    Object.entries(groupedRoutes.value).forEach(([category, routes]) => {
      routes.forEach(route => {
        links.push({
          ROWID: route.path,
          NOMBRE_PADRE: category,
          ICONO_PADRE: categoryIcons[category] || 'folder',
          NOMBRE_RECURSO: route.meta.title,
          URL: route.path,
          ICONO: routeIcons[route.path] || 'arrow_right',
          NOMBRE_PERMISO: 'VER'
        })
      })
    })

    return links
  })

  return {
    user,
    userRole,
    filteredRoutes,
    groupedRoutes,
    menuLinks
  }
}
