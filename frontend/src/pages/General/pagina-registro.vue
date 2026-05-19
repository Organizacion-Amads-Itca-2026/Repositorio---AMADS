<template>
  <div class="login-root window-height window-width row justify-center items-center" style="background-color: #f7fafc;">
    <div class="col-12 col-sm-8 col-md-6 col-lg-4 q-pa-md">
      <q-card class="shadow-4 border-radius-md">
        <q-card-section class="bg-primary text-white text-center q-pa-lg">
          <div class="text-h5 text-weight-bold">Únete a VENTRY</div>
          <div class="text-subtitle2">Crea tu cuenta para comprar boletos</div>
        </q-card-section>

        <q-card-section class="q-pa-lg">
          <q-form @submit="registrar" class="q-gutter-md">
            
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-sm-6">
                <q-input outlined v-model="form.nombres" label="Nombres *" :rules="[val => !!val || 'Requerido']" />
              </div>
              <div class="col-12 col-sm-6">
                <q-input outlined v-model="form.apellidos" label="Apellidos *" :rules="[val => !!val || 'Requerido']" />
              </div>
            </div>

            <q-input outlined v-model="form.email" type="email" label="Correo Electrónico *" :rules="[val => !!val || 'Requerido', val => /.+@.+\..+/.test(val) || 'Email inválido']">
              <template v-slot:prepend><q-icon name="email" /></template>
            </q-input>

            <q-input outlined v-model="form.contrasena" :type="mostrarClave ? 'text' : 'password'" label="Contraseña *" :rules="[val => val.length >= 8 || 'Mínimo 8 caracteres']">
              <template v-slot:prepend><q-icon name="lock" /></template>
              <template v-slot:append>
                <q-icon :name="mostrarClave ? 'visibility_off' : 'visibility'" class="cursor-pointer" @click="mostrarClave = !mostrarClave" />
              </template>
            </q-input>

            <q-input outlined v-model="confirmarClave" :type="mostrarClave ? 'text' : 'password'" label="Confirmar Contraseña *" :rules="[val => val === form.contrasena || 'Las contraseñas no coinciden']">
              <template v-slot:prepend><q-icon name="lock_outline" /></template>
            </q-input>

            <q-btn type="submit" color="primary" class="full-width q-mt-md q-py-sm text-weight-bold" label="Crear Cuenta" :loading="cargando" />
            
            <div class="text-center q-mt-md">
              <span class="text-grey-8">¿Ya tienes una cuenta?</span>
              <q-btn flat color="primary" label="Inicia Sesión" to="/" />
            </div>

          </q-form>
        </q-card-section>
      </q-card>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { authService } from 'src/services/authService'

const router = useRouter()
const $q = useQuasar()

const form = ref({ nombres: '', apellidos: '', email: '', contrasena: '' })
const confirmarClave = ref('')
const mostrarClave = ref(false)
const cargando = ref(false)

const registrar = async () => {
  try {
    cargando.value = true
    await authService.register(form.value)
    
    $q.notify({ type: 'positive', message: '¡Cuenta creada! Ya puedes iniciar sesión.', position: 'top', icon: 'check_circle' })
    router.push('/') // Lo mandamos de vuelta al login
  } catch (error) {
    let msg = 'Error al crear la cuenta.'
    if (error.response?.status === 422) msg = 'El correo ya está registrado o los datos son inválidos.'
    $q.notify({ type: 'negative', message: msg, position: 'bottom' })
  } finally {
    cargando.value = false
  }
}
</script>

<style scoped>
.border-radius-md { border-radius: 12px; }
</style>