import { computed } from 'vue'
import { authService } from 'src/services/authService'

/**
 * Composable para validar permisos del usuario actual
 * Proporciona funciones para verificar si el usuario tiene permisos específicos
 */
export function usePermissions() {
  // Obtener el usuario actual
  const user = computed(() => authService.getUser())

  // Obtener los permisos del usuario
  const userPermissions = computed(() => {
    const userData = user.value
    if (!userData || !userData.permisos) {
      return new Set()
    }
    // Convertir array de permisos a un Set de nombres para búsqueda rápida
    return new Set(userData.permisos.map(p => p.nombre))
  })

  /**
   * Verificar si el usuario tiene un permiso específico
   * @param {string} permissionName - Nombre del permiso a verificar
   * @returns {boolean} true si el usuario tiene el permiso
   */
  const hasPermission = (permissionName) => {
    return userPermissions.value.has(permissionName)
  }

  /**
   * Verificar si el usuario tiene todos los permisos especificados
   * @param {string[]} permissionNames - Array de nombres de permisos
   * @returns {boolean} true si el usuario tiene TODOS los permisos
   */
  const hasAllPermissions = (permissionNames) => {
    if (!Array.isArray(permissionNames)) {
      return false
    }
    return permissionNames.every(permission => userPermissions.value.has(permission))
  }

  /**
   * Verificar si el usuario tiene al menos uno de los permisos especificados
   * @param {string[]} permissionNames - Array de nombres de permisos
   * @returns {boolean} true si el usuario tiene AL MENOS UNO de los permisos
   */
  const hasAnyPermission = (permissionNames) => {
    if (!Array.isArray(permissionNames)) {
      return false
    }
    return permissionNames.some(permission => userPermissions.value.has(permission))
  }

  /**
   * Obtener los permisos por módulo
   * @param {string} moduleName - Nombre del módulo (ej: 'EVENTOS', 'TICKETS')
   * @returns {string[]} Array de nombres de permisos del módulo
   */
  const getPermissionsByModule = (moduleName) => {
    if (!user.value || !user.value.permisos) {
      return []
    }
    return user.value.permisos
      .filter(p => p.modulo === moduleName)
      .map(p => p.nombre)
  }

  /**
   * Obtener todos los módulos para los que el usuario tiene permisos
   * @returns {string[]} Array de nombres de módulos
   */
  const getAvailableModules = () => {
    if (!user.value || !user.value.permisos) {
      return []
    }
    const modules = new Set(user.value.permisos.map(p => p.modulo))
    return Array.from(modules)
  }

  /**
   * Obtener todos los permisos del usuario
   * @returns {object[]} Array de objetos de permisos completos
   */
  const getAllPermissions = () => {
    if (!user.value || !user.value.permisos) {
      return []
    }
    return user.value.permisos
  }

  return {
    userPermissions,
    hasPermission,
    hasAllPermissions,
    hasAnyPermission,
    getPermissionsByModule,
    getAvailableModules,
    getAllPermissions
  }
}
