<template>
  <q-page class="q-pa-md">
    <div class="row justify-center">
      <div class="col-12 col-md-10 col-lg-8">
        <q-card flat bordered>
          <q-card-section class="bg-primary text-white">
            <div class="text-h5">Crear Usuario</div>
            <div class="text-subtitle2">Registro de nuevo usuario en el sistema</div>
          </q-card-section>

          <q-card-section>
            <q-form @submit="guardarUsuario" class="q-gutter-md">
              <div class="row q-col-gutter-md">
                <div class="col-12 col-md-6">
                  <q-input
                    outlined
                    v-model="form.nombres"
                    label="Nombres *"
                    placeholder="Ingrese los nombres"
                    :rules="[val => !!val || 'Campo requerido']"
                    lazy-rules
                  >
                    <template v-slot:prepend>
                      <q-icon name="person" />
                    </template>
                  </q-input>
                </div>

                <div class="col-12 col-md-6">
                  <q-input
                    outlined
                    v-model="form.apellidos"
                    label="Apellidos *"
                    placeholder="Ingrese los apellidos"
                    :rules="[val => !!val || 'Campo requerido']"
                    lazy-rules
                  >
                    <template v-slot:prepend>
                      <q-icon name="person_outline" />
                    </template>
                  </q-input>
                </div>

                <div class="col-12 col-md-6">
                  <q-input
                    outlined
                    v-model="form.email"
                    label="Email *"
                    type="email"
                    placeholder="usuario@ejemplo.com"
                    :rules="[
                      val => !!val || 'Campo requerido',
                      val => /.+@.+\..+/.test(val) || 'Email inválido'
                    ]"
                    lazy-rules
                  >
                    <template v-slot:prepend>
                      <q-icon name="email" />
                    </template>
                  </q-input>
                </div>

                <div class="col-12 col-md-6">
                  <q-select
                    outlined
                    v-model="form.perfilId"
                    :options="perfiles"
                    label="Perfil *"
                    option-label="nombre"
                    option-value="rowid"
                    emit-value
                    map-options
                    :rules="[val => !!val || 'Campo requerido']"
                    lazy-rules
                  >
                    <template v-slot:prepend>
                      <q-icon name="badge" />
                    </template>
                  </q-select>
                </div>

                <div class="col-12 col-md-6">
                  <q-select
                    outlined
                    v-model="form.establecimientoId"
                    :options="establecimientos"
                    label="Establecimiento"
                    option-label="nombre"
                    option-value="rowid"
                    emit-value
                    map-options
                    clearable
                  >
                    <template v-slot:prepend>
                      <q-icon name="store" />
                    </template>
                  </q-select>
                </div>

                <div class="col-12 col-md-6">
                  <q-input
                    outlined
                    v-model="form.contrasena"
                    :type="mostrarPassword ? 'text' : 'password'"
                    label="Contraseña *"
                    placeholder="Mínimo 8 caracteres"
                    :rules="passwordRules"
                    lazy-rules
                    @focus="validarPassword = true"
                  >
                    <template v-slot:prepend>
                      <q-icon name="lock" />
                    </template>
                    <template v-slot:append>
                      <q-icon
                        :name="mostrarPassword ? 'visibility_off' : 'visibility'"
                        class="cursor-pointer"
                        @click="mostrarPassword = !mostrarPassword"
                      />
                    </template>
                  </q-input>

                  <div v-if="form.contrasena" class="q-mt-sm">
                    <div class="text-caption">Fortaleza de la contraseña:</div>
                    <q-linear-progress
                      :value="passwordStrength"
                      :color="passwordStrengthColor"
                      class="q-mt-xs"
                    />
                    <div class="text-caption" :style="{ color: passwordStrengthColor }">
                      {{ passwordStrengthText }}
                    </div>
                  </div>
                </div>
              </div>

              <div class="row q-col-gutter-sm q-mt-md">
                <div class="col-12 col-md-6">
                  <q-btn
                    label="Limpiar"
                    color="grey"
                    outline
                    class="full-width"
                    @click="limpiarFormulario"
                    icon="clear"
                  />
                </div>
                <div class="col-12 col-md-6">
                  <q-btn
                    type="submit"
                    label="Guardar Usuario"
                    color="primary"
                    class="full-width"
                    icon="save"
                    :loading="cargando"
                  />
                </div>
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { usuarioService } from 'src/services/usuarioService'
import { api } from 'boot/axios'

const $q = useQuasar()

// Estado del formulario
const form = ref({
  nombres: '',
  apellidos: '',
  email: '',
  contrasena: '',
  perfilId: null,
  establecimientoId: null
})

const mostrarPassword = ref(false)
const validarPassword = ref(false)
const cargando = ref(false)
const perfiles = ref([])
const establecimientos = ref([])

// Reglas de validación de contraseña
const passwordRules = [
  val => !!val || 'La contraseña es requerida',
  val => val.length >= 8 || 'Mínimo 8 caracteres',
  val => val.length <= 50 || 'Máximo 50 caracteres',
  val => /[A-Z]/.test(val) || 'Debe contener al menos una mayúscula',
  val => /[a-z]/.test(val) || 'Debe contener al menos una minúscula',
  val => /\d/.test(val) || 'Debe contener al menos un número',
  val => /[!@#$%^&*(),.?":{}|<>]/.test(val) || 'Debe contener al menos un símbolo especial'
]

// Medidor de fortaleza de contraseña
const passwordStrength = computed(() => {
  const password = form.value.contrasena
  if (!password) return 0

  let strength = 0
  if (password.length >= 8) strength += 0.2
  if (password.length >= 12) strength += 0.1
  if (/[A-Z]/.test(password)) strength += 0.2
  if (/[a-z]/.test(password)) strength += 0.1
  if (/\d/.test(password)) strength += 0.2
  if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 0.2

  return Math.min(strength, 1)
})

const passwordStrengthColor = computed(() => {
  const strength = passwordStrength.value
  if (strength < 0.4) return 'red'
  if (strength < 0.7) return 'orange'
  return 'green'
})

const passwordStrengthText = computed(() => {
  const strength = passwordStrength.value
  if (strength < 0.4) return 'Débil'
  if (strength < 0.7) return 'Media'
  return 'Fuerte'
})

// Cargar perfiles
const cargarPerfiles = async () => {
  try {
    const response = await api.get('/api/seg/perfiles')
    perfiles.value = response.data
  } catch (error) {
    console.error('Error al cargar perfiles:', error)
    $q.notify({
      type: 'negative',
      message: 'Error al cargar los perfiles',
      icon: 'error'
    })
  }
}

// Cargar establecimientos
const cargarEstablecimientos = async () => {
  try {
    const response = await api.get('/api/adm/establecimientos')
    establecimientos.value = response.data
  } catch (error) {
    console.error('Error al cargar establecimientos:', error)
    $q.notify({
      type: 'negative',
      message: 'Error al cargar los establecimientos',
      icon: 'error'
    })
  }
}

// Guardar usuario
const guardarUsuario = async () => {
  try {
    cargando.value = true

    const payload = {
      nombres: form.value.nombres,
      apellidos: form.value.apellidos,
      email: form.value.email,
      contrasena: form.value.contrasena, // Coincide con el DTO
      perfilId: form.value.perfilId,
      establecimientoId: form.value.establecimientoId
    }

    await usuarioService.createUsuario(payload)

    $q.notify({
      type: 'positive',
      message: 'Usuario creado exitosamente',
      icon: 'check_circle'
    })

    limpiarFormulario()
  } catch (error) {
    console.error('Error al guardar usuario:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || error.response?.data?.mensaje || 'Error al crear el usuario',
      icon: 'error'
    })
  } finally {
    cargando.value = false
  }
}

// Limpiar formulario
const limpiarFormulario = () => {
  form.value = {
    nombres: '',
    apellidos: '',
    email: '',
    contrasena: '',
    perfilId: null,
    establecimientoId: null
  }
  validarPassword.value = false
}

// Inicialización
onMounted(async () => {
  await cargarPerfiles()
  await cargarEstablecimientos()
})
</script>

<style lang="scss" scoped>
</style>
