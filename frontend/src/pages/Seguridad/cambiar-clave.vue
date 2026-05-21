<template>
  <div class="q-pa-md">
    <q-card class="q-mx-auto" style="max-width: 600px">
      <q-card-section class="bg-primary text-white">
        <div class="text-h5">Cambiar Contraseña</div>
        <div class="text-subtitle2">Actualiza tu contraseña de acceso</div>
      </q-card-section>

      <q-card-section>
        <q-form ref="formRef">
          <q-input
            v-model="form.passwordActual"
            :type="showPasswordActual ? 'text' : 'password'"
            label="Contraseña Actual *"
            outlined
            class="q-mb-md"
            :rules="[val => !!val || 'Campo requerido']"
          >
            <template v-slot:append>
              <q-icon
                :name="showPasswordActual ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                @click="showPasswordActual = !showPasswordActual"
              />
            </template>
          </q-input>

          <q-input
            v-model="form.passwordNueva"
            :type="showPasswordNueva ? 'text' : 'password'"
            label="Nueva Contraseña *"
            outlined
            class="q-mb-md"
            :rules="passwordRules"
            hint="Mínimo 8 caracteres, 1 mayúscula, 1 número y 1 símbolo"
          >
            <template v-slot:append>
              <q-icon
                :name="showPasswordNueva ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                @click="showPasswordNueva = !showPasswordNueva"
              />
            </template>
          </q-input>

          <q-input
            v-model="form.passwordConfirmar"
            :type="showPasswordConfirmar ? 'text' : 'password'"
            label="Confirmar Nueva Contraseña *"
            outlined
            class="q-mb-md"
            :rules="[
              val => !!val || 'Campo requerido',
              val => val === form.passwordNueva || 'Las contraseñas no coinciden'
            ]"
          >
            <template v-slot:append>
              <q-icon
                :name="showPasswordConfirmar ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                @click="showPasswordConfirmar = !showPasswordConfirmar"
              />
            </template>
          </q-input>

          <div class="q-mt-md">
            <q-linear-progress
              :value="passwordStrength"
              :color="passwordStrengthColor"
              size="8px"
              class="q-mb-sm"
            />
            <div class="text-caption" :class="`text-${passwordStrengthColor}`">
              Fortaleza: {{ passwordStrengthText }}
            </div>
          </div>
        </q-form>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn flat label="Cancelar" color="grey" @click="resetForm" :disable="cargando" />
        <q-btn label="Cambiar Contraseña" color="primary" @click="cambiarPassword" :loading="cargando" />
      </q-card-actions>
    </q-card>
  </div>
</template>

<script setup>
//mportamos nextTick de Vue
import { ref, computed, nextTick } from 'vue' 
import { useQuasar } from 'quasar'
import { usuarioService } from 'src/services/usuarioService'
import { authService } from 'src/services/authService'

const $q = useQuasar()
const formRef = ref(null)

// AGREGADO: Variable para controlar el estado de carga
const cargando = ref(false) 

const showPasswordActual = ref(false)
const showPasswordNueva = ref(false)
const showPasswordConfirmar = ref(false)

const form = ref({
  passwordActual: '',
  passwordNueva: '',
  passwordConfirmar: '',
  email: "admin@ventry.com"
})

const passwordRules = [
  val => !!val || 'Campo requerido',
  val => val.length >= 8 || 'Mínimo 8 caracteres',
  val => val.length <= 18 || 'Máximo 18 caracteres',
  val => /[A-Z]/.test(val) || 'Debe contener al menos una letra mayúscula',
  val => /[a-z]/.test(val) || 'Debe contener al menos una letra minúscula',
  val => /\d/.test(val) || 'Debe contener al menos un número',
  val => /[!@#$%^&*(),.?":{}|<>]/.test(val) || 'Debe contener al menos un símbolo'
]

const passwordStrength = computed(() => {
  const password = form.value.passwordNueva
  if (!password) return 0

  let strength = 0
  if (password.length >= 8) strength += 0.2
  if (password.length >= 12) strength += 0.2
  if (/[A-Z]/.test(password)) strength += 0.2
  if (/[a-z]/.test(password)) strength += 0.1
  if (/\d/.test(password)) strength += 0.15
  if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 0.15

  return Math.min(strength, 1)
})

const passwordStrengthColor = computed(() => {
  if (passwordStrength.value < 0.3) return 'negative'
  if (passwordStrength.value < 0.6) return 'warning'
  if (passwordStrength.value < 0.8) return 'info'
  return 'positive'
})

const passwordStrengthText = computed(() => {
  if (passwordStrength.value < 0.3) return 'Débil'
  if (passwordStrength.value < 0.6) return 'Media'
  if (passwordStrength.value < 0.8) return 'Fuerte'
  return 'Muy Fuerte'
})

const cambiarPassword = async () => {
  const valid = await formRef.value.validate()
  if (!valid) {
    $q.notify({
      type: 'warning',
      message: 'Por favor complete todos los campos correctamente'
    })
    return
  }

  try {
    // Activamos el spinner del botón
    cargando.value = true 
    
    const user = authService.getUser()
    await usuarioService.cambiarPassword({
      email: user.email,
      passwordActual: form.value.passwordActual,
      passwordNueva: form.value.passwordNueva
    })

    $q.notify({
      type: 'positive',
      message: 'Contraseña cambiada exitosamente',
      icon: 'check_circle'
    })

    await resetForm()
  } catch (error) {
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error al cambiar contraseña',
      icon: 'error'
    })
  } finally {
    // Apagamos el spinner pase lo que pase
    cargando.value = false 
  }
}

const resetForm = async () => {
  form.value = {
    passwordActual: '',
    passwordNueva: '',
    passwordConfirmar: '',
    email: "admin@ventry.com"
  }
  
  
  await nextTick() 
  formRef.value?.resetValidation()
}
</script>