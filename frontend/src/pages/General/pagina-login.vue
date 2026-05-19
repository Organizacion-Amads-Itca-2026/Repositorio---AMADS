<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { authService } from 'src/services/authService'
import img_login from '../../assets/inicio.png'
import icon_ventry from '../../assets/ventry.png'

const $q = useQuasar()
const router = useRouter()

const loginSuccessOverlay = ref(false)
const email = ref('')
const clave = ref('')
const validacion = ref({
  email: false,
  clave: false,
})

const login = async () => {
  // Validar campos
  validacion.value.email = !email.value
  validacion.value.clave = !clave.value

  if (validacion.value.email || validacion.value.clave) {
    $q.notify({
      type: 'negative',
      message: 'Email y contraseña son obligatorios',
      position: 'bottom',
      timeout: 3000,
      icon: 'error',
    })
    return
  }

  try {
    $q.loading.show({ message: 'Verificando credenciales...' })

    await authService.login(email.value, clave.value)

    $q.loading.hide()
    loginSuccessOverlay.value = true

    $q.notify({
      type: 'positive',
      message: 'Inicio de sesión exitoso',
      position: 'bottom',
      timeout: 2000,
      icon: 'check_circle',
    })

    // Redirigir según el rol del usuario
    //setTimeout(() => {
      //const role = authService.getUserRole()
      //if (role === 'ADMIN' || role === 'ADMINISTRADOR') {
        //router.push('/inicio')
      //} else {
        //router.push('/inicio-usuarios')
      //}
    //}, 2000)


    // Redirigir según el rol del usuario
    setTimeout(() => {
      const role = authService.getUserRole()
      if (role === 'ADMIN' || role === 'ADMINISTRADOR') {
        router.push('/eventos') // <--- AHORA VA DIRECTO A EVENTOS
      } else {
        router.push('/inicio-usuarios')
      }
    }, 2000)

  } catch (err) {
    $q.loading.hide()
    console.error('Error al iniciar sesión:', err)
    $q.notify({
      type: 'negative',
      message: err.response?.data?.message || 'Error al iniciar sesión. Verifique sus credenciales.',
      position: 'bottom',
      timeout: 40000,
      icon: 'error',
    })
  }
}

onMounted(() => {
  // Si ya está autenticado, redirigir
  if (authService.isAuthenticated()) {
    const role = authService.getUserRole()
    if (role === 'ADMIN' || role === 'ADMINISTRADOR') {
      router.push('/inicio')
    } else {
      router.push('/inicio-usuarios')
    }
  }
})
</script>

<template>
  <div v-if="loginSuccessOverlay" class="success-overlay">
    <div class="overlay-content">
      <q-spinner-hourglass size="80px" color="primary" />
      <p class="text-h6 q-mt-md">Bienvenido a VENTRY</p>
    </div>
  </div>

  <div class="login-root">
    <div class="box-root flex-flex flex-direction--column" style="min-height: 100vh; flex-grow: 1">
      <div class="loginbackground box-background--aqua300 padding-top--64">
        <div class="loginbackground-gridContainer">
          <div class="box-root flex-flex" style="grid-area: top / start / 8 / end">
            <div
              class="box-root"
              style="
                background-image: linear-gradient(white 0%, rgb(247, 250, 252) 33%);
                flex-grow: 1;
              "
            ></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 2 / 14 / auto / 19">
            <div class="box-root box-background--aqua100" style="flex-grow: 1"></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 4 / 2 / auto / 5">
            <div
              class="box-root box-divider--light-all-2 animationLeftRight tans3s"
              style="flex-grow: 1"
            ></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 6 / start / auto / 2">
            <div class="box-root box-background--aqua500" style="flex-grow: 1"></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 7 / start / auto / 4">
            <div
              class="box-root box-background--aqua100 animationLeftRight"
              style="flex-grow: 1"
            ></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 8 / 4 / auto / 6">
            <div
              class="box-root box-background--aqua700 animationLeftRight tans3s"
              style="flex-grow: 1"
            ></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 2 / 15 / auto / end">
            <div
              class="box-root box-background--aqua900 animationRightLeft tans4s"
              style="flex-grow: 1"
            ></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 3 / 14 / auto / end">
            <div
              class="box-root box-background--aqua100 animationRightLeft"
              style="flex-grow: 1"
            ></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 4 / 17 / auto / 20">
            <div
              class="box-root box-background--aqua700 animationRightLeft tans4s"
              style="flex-grow: 1"
            ></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 5 / 14 / auto / 17">
            <div
              class="box-root box-divider--light-all-2 animationRightLeft tans3s"
              style="flex-grow: 1"
            ></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 9 / 2 / auto / 5">
            <div
              class="box-root box-background--aqua300 animationLeftRight tans3s"
              style="flex-grow: 1"
            ></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 10 / 3 / auto / 6">
            <div
              class="box-root box-background--aqua500 animationLeftRight tans4s"
              style="flex-grow: 1"
            ></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 11 / 5 / auto / 7">
            <div
              class="box-root box-background--aqua700 animationRightLeft"
              style="flex-grow: 1"
            ></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 12 / auto / auto / end">
            <div class="box-root box-background--aqua100" style="flex-grow: 1"></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 13 / 2 / auto / 4">
            <div class="box-root box-background--aqua900 tans3s" style="flex-grow: 1"></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 14 / 4 / auto / 7">
            <div
              class="box-root box-background--aqua300 animationLeftRight tans4s"
              style="flex-grow: 1"
            ></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 15 / 6 / auto / 9">
            <div class="box-root box-background--aqua100" style="flex-grow: 1"></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 16 / 8 / auto / 11">
            <div
              class="box-root box-background--aqua500 animationRightLeft tans3s"
              style="flex-grow: 1"
            ></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 17 / 10 / auto / 13">
            <div
              class="box-root box-background--aqua700 animationLeftRight tans4s"
              style="flex-grow: 1"
            ></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 18 / 12 / auto / 15">
            <div class="box-root box-background--aqua900" style="flex-grow: 1"></div>
          </div>
          <div class="box-root flex-flex" style="grid-area: 19 / 14 / auto / 17">
            <div
              class="box-root box-background--aqua300 animationRightLeft tans3s"
              style="flex-grow: 1"
            ></div>
          </div>
        </div>
      </div>
      <div
        class="box-root padding-top--24 flex-flex flex-direction--column"
        style="flex-grow: 1; z-index: 9"
      >
        <div
          class="box-root padding-top--24 flex-flex flex-direction--column"
          style="flex-grow: 1; z-index: 9"
        >
          <div class="box-root flex-flex flex-justifyContent--center">
            <h1></h1>
          </div>
          <div class="formbg-outer">
            <div class="formbg-container">
              <div class="formbg-image">
                <img :src="img_login" alt="Imagen de fondo" class="side-image" />
              </div>
              <div class="formbg">
                <div class="formbg-inner padding-horizontal--38">
                  <img :src="icon_ventry" alt="Icono" class="icon" />
                  <span class="padding-bottom--15">Iniciar Sesión</span>
                  <form id="stripe-login" @submit.prevent="login">
                    <div class="field padding-bottom--24">
                      <label for="email">Correo</label>
                      <input id="email" type="email" name="email" v-model="email" required />
                      <div class="error-message" v-if="validacion.email" style="display: block">
                        Correo es requerido
                      </div>
                    </div>
                    <div class="field padding-bottom--24">
                      <div class="grid--50-50">
                        <label for="password">Contraseña</label>
                      </div>
                      <input
                        id="password"
                        type="password"
                        name="password"
                        v-model="clave"
                        required
                      />
                      <div class="error-message" v-if="validacion.clave" style="display: block">
                        La contraseña es requerida
                      </div>
                    </div>
                    <div class="field padding-bottom--5">
                      <input type="submit" name="submit" value="Iniciar Sesión" />
                    </div>
                    <div class="text-center q-mt-md" style="text-align: center; margin-top: 20px;">
      <span style="font-size: 14px; font-weight: normal; margin-left: 0; color: #697386;">¿No tienes una cuenta?</span>
      <br>
      <q-btn flat color="primary" label="Regístrarse" to="/registro" />
    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.success-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  flex-direction: column;
}

.overlay-content {
  text-align: center;
}

* {
  padding: 0;
  margin: 0;
  color: #1a1f36;
  box-sizing: border-box;
  word-wrap: break-word;
  font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica Neue, Ubuntu,
    sans-serif;
}

body {
  min-height: 100%;
  background-color: #ffffff;
}
.formbg-outer {
  padding-block: 2%;
}
h1 {
  letter-spacing: -1px;
}
a {
  color: #4dd0e1;
  text-decoration: unset;
}
.login-root {
  background: #fff;
  display: flex;
  width: 100%;
  min-height: 100vh;
  overflow: hidden;
}
.loginbackground {
  min-height: 692px;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  top: 0;
  z-index: 0;
  overflow: hidden;
}
.flex-flex {
  display: flex;
}
.align-center {
  align-items: center;
}
.center-center {
  align-items: center;
  justify-content: center;
}
.box-root {
  box-sizing: border-box;
}
.flex-direction--column {
  -ms-flex-direction: column;
  flex-direction: column;
}
.loginbackground-gridContainer {
  display: grid;
  grid-template-columns: [start] 1fr [left-gutter] repeat(16, 86.6px) [left-gutter] 1fr [end];
  grid-template-rows:
    [top] 1fr
    [top-gutter] repeat(20, 64px)
    [bottom-gutter] 1fr
    [bottom];
  justify-content: center;
  margin: 0 -2%;
  transform: rotate(-12deg) skew(-12deg);
}

.box-divider--light-all-2 {
  box-shadow: inset 0 0 0 2px #cb7ed0;
}

.box-background--aqua100 {
  background-color: #fea5f7;
}
.box-background--aqua300 {
  background-color: #ffffff;
}
.box-background--aqua500 {
  background-color: #745fa0;
}
.box-background--aqua700 {
  background-color: #334877;
}
.box-background--aqua900 {
  background-color: #003658;
}

.padding-top--64 {
  padding-top: 64px;
}
.padding-top--24 {
  padding-top: 24px;
}
.padding-top--48 {
  padding-top: 48px;
}
.padding-bottom--24 {
  padding-bottom: 24px;
}
.padding-horizontal--48 {
  padding: 48px;
}
.padding-bottom--15 {
  padding-bottom: 15px;
}

.flex-justifyContent--center {
  -ms-flex-pack: center;
  justify-content: center;
}

.logo-link {
  width: 4em;
}
.icon {
  width: 200px;
  height: 90px;
}

.formbg-container {
  display: flex;
  align-items: center;
  justify-content: center;
  max-width: 800px;
  margin: 0 auto;
  background: #f1f1f1f1;
  border-radius: 4px;
  box-shadow: rgba(60, 66, 87, 0.12) 0px 7px 14px 0px, rgba(0, 0, 0, 0.12) 0px 3px 6px 0px;
  padding: 20px;
}

.formbg-image {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.side-image {
  max-width: 100%;
  max-height: 400px;
  object-fit: contain;
}

.formbg {
  flex: 1;
  margin: 0;
  width: 100%;
  max-width: 400px;
}

@media screen and (max-width: 600px) {
  .formbg-container {
    flex-direction: column;
    padding: 10px;
  }

  .formbg-image,
  .formbg {
    flex: none;
    width: 100%;
  }

  .side-image {
    max-height: 100px;
  }

  .formbg {
    max-width: 70%;
  }
}
span {
  display: block;
  margin-left: 50px;
  font-size: 20px;
  line-height: 28px;
  color: black;
  font-weight: bold;
  font-style: italic;
}
label {
  margin-bottom: 10px;
  color: black;
}
.reset-pass a,
label {
  font-size: 14px;
  font-weight: 600;
  width: 100%;
  display: block;
}
.reset-pass > a {
  text-align: right;
  margin-bottom: 10px;
}
.grid--50-50 {
  display: grid;
  grid-template-columns: 50% 50%;
  align-items: center;
}

.field input {
  font-size: 16px;
  line-height: 28px;
  padding: 8px 16px;
  width: 100%;
  min-height: 44px;
  border: unset;
  border-radius: 4px;
  outline-color: rgb(84 105 212 / 0.5);
  background-color: rgb(255, 255, 255);
  box-shadow: rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 0px 0px 0px,
    rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(60, 66, 87, 0.16) 0px 0px 0px 1px,
    rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 0px 0px 0px,
    rgba(0, 0, 0, 0) 0px 0px 0px 0px;
}

input[type='submit'] {
  background-color: #cc7dce;
  box-shadow: rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 0px 0px 0px,
    rgba(0, 0, 0, 0.12) 0px 1px 1px 0px, rgb(204, 125, 206) 0px 0px 0px 1px,
    rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 0px 0px 0px,
    rgba(60, 66, 87, 0.08) 0px 2px 5px 0px;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
}

select.filled,
input[type='email'],
input[type='text'],
input[type='password'] {
  font-size: 16px;
  line-height: 28px;
  padding: 8px 16px;
  width: 100%;
  min-height: 44px;
  border: unset;
  border-radius: 4px;
  outline-color: #cc7dce;
  background-color: #fff;
  box-shadow: rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 0px 0px 0px,
    rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(60, 66, 87, 0.16) 0px 0px 0px 1px,
    rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 0px 0px 0px,
    rgba(0, 0, 0, 0) 0px 0px 0px 0px;
}

select.filled {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  cursor: pointer;
}

.error-message {
  color: rgb(255, 208, 0);
  font-size: 0.875rem;
  margin-top: 0.25rem;
}
.field-checkbox input {
  width: 20px;
  height: 15px;
  margin-right: 5px;
  box-shadow: unset;
  min-height: unset;
}
.field-checkbox label {
  display: flex;
  align-items: center;
  margin: 0;
}
a.ssolink {
  display: block;
  text-align: center;
  font-weight: 600;
}
.footer-link span {
  font-size: 14px;
  text-align: center;
}
.listing a {
  color: #697386;
  font-weight: 600;
  margin: 0 10px;
}

.animationRightLeft {
  animation: animationRightLeft 2s ease-in-out infinite;
}
.animationLeftRight {
  animation: animationLeftRight 2s ease-in-out infinite;
}
.tans3s {
  animation: animationLeftRight 3s ease-in-out infinite;
}
.tans4s {
  animation: animationLeftRight 4s ease-in-out infinite;
}

@keyframes animationLeftRight {
  0% {
    transform: translateX(0px);
  }
  50% {
    transform: translateX(1000px);
  }
  100% {
    transform: translateX(0px);
  }
}

@keyframes animationRightLeft {
  0% {
    transform: translateX(0px);
  }
  50% {
    transform: translateX(-1000px);
  }
  100% {
    transform: translateX(0px);
  }
}

@media screen and (max-width: 600px) {
  .loginbackground-gridContainer {
    grid-template-columns: [start] 1fr [left-gutter] repeat(8, 40px) [left-gutter] 1fr [end];
    grid-template-rows:
      [top] 1fr
      [top-gutter] repeat(20, 48px)
      [bottom-gutter] 1fr
      [bottom];
  }

  h1 {
    font-size: 2.5rem;
  }

  .box-root {
    padding-block: 0% !important;
  }

  .formbg-outer {
    padding-block: 0% !important;
  }
  .reset-pass a,
  label {
    font-size: 12px;
    font-weight: 600;
    width: 100%;
    display: block;
  }

  html,
  body {
    height: 100vh;
    margin: 0;
    padding: 0;
    overflow: hidden;
  }

  .formbg,
  .login-root {
    height: 100vh;
    width: 100vw;
    opacity: 0.95;
    border-radius: 0;
    padding: 0;
    overflow: hidden;
  }

  .loginbackground-gridContainer {
    display: grid;
    grid-template-columns: [start] 1fr [left-gutter] repeat(30, 100.6px) [left-gutter] 1fr [end];
    grid-template-rows:
      [top] 1fr
      [top-gutter] repeat(20, 64px)
      [bottom-gutter] 1fr
      [bottom];
    justify-content: center;
    margin: 0 -2%;
    transform: rotate(-12deg) skew(-12deg);
  }

  .logo-link {
    width: 6em;
    padding-top: 15px;
  }
}
</style>
