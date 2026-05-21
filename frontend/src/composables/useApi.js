import { ref } from 'vue'
import { api } from 'boot/axios'
import { useQuasar } from 'quasar'

export function useApi() {
  const $q = useQuasar()
  const loading = ref(false)
  const error = ref(null)

  const handleError = (err, customMessage = 'Error en la operación') => {
    console.error(customMessage, err)
    const message = err.response?.data?.message || err.response?.data?.mensaje || customMessage
    $q.notify({
      type: 'negative',
      message,
      position: 'top',
      timeout: 3000,
    })
    error.value = message
    return null
  }

  const get = async (url, config = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.get(url, config)
      return response.data
    } catch (err) {
      return handleError(err, `Error al obtener datos de ${url}`)
    } finally {
      loading.value = false
    }
  }

  const post = async (url, data, config = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.post(url, data, config)
      return response.data
    } catch (err) {
      return handleError(err, `Error al crear registro en ${url}`)
    } finally {
      loading.value = false
    }
  }

  const put = async (url, data, config = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.put(url, data, config)
      return response.data
    } catch (err) {
      return handleError(err, `Error al actualizar registro en ${url}`)
    } finally {
      loading.value = false
    }
  }

  const del = async (url, config = {}) => {
    loading.value = true
    error.value = null
    try {
      const response = await api.delete(url, config)
      return response.data
    } catch (err) {
      return handleError(err, `Error al eliminar registro en ${url}`)
    } finally {
      loading.value = false
    }
  }

  return {
    loading,
    error,
    get,
    post,
    put,
    del,
  }
}
