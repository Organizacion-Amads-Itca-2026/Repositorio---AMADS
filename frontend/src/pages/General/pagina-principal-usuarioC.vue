
<template>

  <q-page class="main-page">
    <!-- Hero Banner con Carrusel -->
    <div class="hero-section">
      <q-carousel
        animated
        v-model="slide"
        height="500px"
        navigation
        infinite
        autoplay
        class="carousel-banner rounded-lg shadow-lg"
      >
        <q-carousel-slide
          v-for="movie in carouselMovies"
          :key="movie.id"
          :name="movie.id"
          :img-src="movie.image"
          class="carousel-slide-custom"
        >
          <div class="carousel-overlay"></div>
          <div class="carousel-content">
            <div class="text-h3 text-weight-bold text-white text-center carousel-title">
              {{ movie.title }}
            </div>
          </div>
        </q-carousel-slide>
      </q-carousel>
    </div>

    <!-- Sección de Búsqueda Premium -->
    <div class="search-section q-mt-lg">
      <div class="container">
        <q-input
          v-model="searchQuery"
          outlined
          dense
          :dark="false"
          label="Busca tu evento favorito..."
          class="search-input shadow-md"
        >
          <template v-slot:prepend>
            <q-icon name="search" color="primary" size="md" />
          </template>
          <template v-slot:append>
            <q-icon v-if="searchQuery" name="close" @click="handleClear" class="cursor-pointer" />
          </template>
        </q-input>
      </div>
    </div>
    <!-- Lista de eventos  -->
    <section class="events-section q-mt-xl" v-for="tipo in tipoEventos" :key="tipo.id_tipo_evento">
      <div class="section-header">
        <q-icon name="celebration" color="primary" size="lg" class="q-mr-md" />
        <h2 id="festivales-titulo" class="section-title">{{ tipo.nombre }}</h2>
      </div>
      <div
        v-if="allEventos.filter(e => e.tipoEvento == tipo.id_tipo_evento).length > 0"
        ref="scrollContainerFestivales"
        class="events-scroll-container"
      >
        <div
          v-for="eventos in allEventos.filter(e => e.tipoEvento == tipo.id_tipo_evento)"
          :key="eventos.id_evento"
          class="event-card-wrapper"
        >
          <q-card class="event-card">
            <div class="event-image-container">
              <!-- Cambiar el src porque no es dinamico aun -->
              <q-img :src="icono_empresa" height="280px" fit="cover" class="event-image" />
              <div class="event-overlay"></div>
            </div>
            <q-card-section class="event-card-content">
              <h3 class="event-title">{{ eventos.nombre }}</h3>
              <q-btn
                label="Adquirir entradas"
                color="primary"
                unelevated
                size="md"
                class="full-width event-btn q-mt-md"
                @click="abrirModalCompra(eventos)"
              />
            </q-card-section>
          </q-card>
        </div>
      </div>
      <div v-else class="empty-state">
        <q-icon name="festival" size="64px" color="grey-5" />
        <p class="text-subtitle1 text-grey-7 q-mt-md">
          No hay festivales disponibles en este momento
        </p>
      </div>
    </section>

    <!-- Modal de Compra -->
    <q-dialog v-model="dialog" size="lg">
      <q-card class="purchase-modal">
        <!-- Header -->
        <q-card-section class="bg-primary text-white q-pa-md">
          <div class="row items-center">
            <div class="col">
              <div class="text-h6">Compra de Entradas</div>
              <p class="text-subtitle2 q-mb-none">
                {{ eventoSeleccionadoModal?.nombre || 'Selecciona un evento' }}
              </p>
            </div>
            <div class="col-auto">
              <q-btn icon="close" flat round dense v-close-popup />
            </div>
          </div>
        </q-card-section>

        <q-card-section class="q-pa-md modal-scroll">
          <div class="row q-col-gutter-md">
            <!-- Columna Izquierda: Selección de Categorías -->
            <div class="col-12 col-md-6">
              <div class="text-h6 q-mb-md">Categorías de Entradas</div>
              <!-- Listado de Categorías -->
              <div v-if="eventoSeleccionadoModal" class="q-mb-md">
                <q-list bordered separator>
                  <q-item
                    v-for="categoria in categoriasModal"
                    :key="categoria.id_categoria_ticket"
                    clickable
                    @click="agregarAlCarritoModal(categoria)"
                    class="categoria-item hover-effect"
                  >
                    <q-item-section>
                      <q-item-label>{{ categoria.nombre }}</q-item-label>
                      <q-item-label caption>
                        Disponibles: {{ categoria.cantidad_disponible }}
                      </q-item-label>
                    </q-item-section>
                    <q-item-section side>
                      <q-item-label class="text-weight-bold text-positive"
                        >${{ categoria.precio_base }}</q-item-label
                      >
                    </q-item-section>
                  </q-item>
                </q-list>
              </div>
              <div v-else class="text-center text-grey q-py-lg">
                No hay categorias para este evento. ¡Esperalo proximamente!
              </div>
            </div>

            <!-- Columna Derecha: Carrito y Pago -->
            <div class="col-12 col-md-6">
              <div class="text-h6 q-mb-md">Carrito de Compra</div>

              <!-- Carrito -->
              <div class="carrito-section q-mb-lg" style="max-height: 300px; overflow-y: auto">
                <q-list v-if="carritoModal.length > 0" bordered separator>
                  <q-item v-for="(item, idx) in carritoModal" :key="idx">
                    <q-item-section>
                      <q-item-label>{{ item.nombre }}</q-item-label>
                      <q-item-label caption>${{ item.precio_base }} c/u</q-item-label>
                    </q-item-section>
                    <q-item-section side top>
                      <q-input
                        v-model.number="item.cantidad"
                        type="number"
                        min="1"
                        dense
                        style="width: 60px"
                        @update:model-value="calcularTotalModal"
                      />
                    </q-item-section>
                    <q-item-section side top>
                      <div class="text-weight-bold">
                        ${{ (item.precio_base * item.cantidad).toFixed(2) }}
                      </div>
                    </q-item-section>
                    <q-item-section side top>
                      <q-btn
                        icon="delete"
                        flat
                        round
                        dense
                        color="negative"
                        @click="eliminarDelCarritoModal(idx)"
                      />
                    </q-item-section>
                  </q-item>
                </q-list>
                <div v-else class="text-center text-grey q-py-lg">
                  <q-icon name="shopping_cart" size="48px" />
                  <p>Carrito vacío</p>
                </div>
              </div>

              <!-- Totales -->
              <q-card flat bordered class="q-mb-md bg-grey-1">
                <q-card-section>
                  <div class="row justify-between q-mb-sm">
                    <q-input
                      v-model="cuponCodigo"
                      label="Cupón de descuento"
                      dense
                      class="q-mb-md"
                    />

                    <q-btn
                      label="Aplicar cupón"
                      class="q-mb-md bg-primary text-white"
                      @click="aplicarCupon(eventoSeleccionadoModal.rowid)"
                    />
                  </div>

                  <div class="row justify-between q-mb-sm">
                    <span>Descuento:</span>
                    <span class="text-weight-bold">${{ descuentoCalculado }}</span>
                  </div>
                  <q-separator class="q-my-sm" />
                  <div class="row justify-between q-mb-sm">
                    <span>Subtotal:</span>
                    <span class="text-weight-bold">${{ subtotalModal.toFixed(2) }}</span>
                  </div>
                  <q-separator class="q-my-sm" />
                  <div class="row justify-between">
                    <span>Total:</span>
                    <span class="text-h6 text-weight-bold text-primary"
                      >${{ totalModal.toFixed(2) }}</span
                    >
                  </div>
                </q-card-section>
              </q-card>

              <!-- Datos de Pago -->
              <div class="q-mb-md">
                <div class="text-subtitle2 text-weight-bold q-mb-md">Datos de Pago</div>
                <q-form ref="formPagoModal">
                  <q-input
                    v-model="datosCliente.nombres"
                    label="Nombres *"
                    outlined
                    dense
                    class="q-mb-md"
                    :rules="[(val) => !!val || 'Campo requerido']"
                  />
                  <q-input
                    v-model="datosCliente.email"
                    label="Email *"
                    type="email"
                    outlined
                    dense
                    class="q-mb-md"
                    :rules="[(val) => !!val || 'Email requerido']"
                  />
                  <q-input
                    v-model="datosCliente.telefono"
                    label="Teléfono *"
                    outlined
                    dense
                    class="q-mb-md"
                    mask="####-####"
                    :rules="[(val) => !!val || 'Teléfono requerido']"
                  />
                </q-form>
              </div>

              <!-- Método de Pago -->
              <div class="q-mb-md">
                <div class="text-subtitle2 text-weight-bold q-mb-md">Método de Pago</div>
                <q-option-group
                  v-model="metodoPago"
                  :options="metodoPagoOptions"
                  color="primary"
                  inline
                />
              </div>

              <!-- Sección de Tarjeta de Crédito -->
              <div
                v-if="metodoPago === 'tarjeta'"
                class="q-mb-md bg-blue-1 q-pa-md rounded-borders"
              >
                <div class="text-subtitle2 text-weight-bold q-mb-md">Datos de Tarjeta</div>
                <q-input
                  v-model="datosTarjeta.numero"
                  label="Número de Tarjeta *"
                  outlined
                  dense
                  mask="#### #### #### ####"
                  class="q-mb-md"
                  :rules="[(val) => !!val || 'Número requerido']"
                />
                <div class="row q-col-gutter-md q-mb-md">
                  <div class="col-6">
                    <q-input
                      v-model="datosTarjeta.vencimiento"
                      label="Vencimiento *"
                      outlined
                      dense
                      mask="##/##"
                      placeholder="MM/YY"
                      :rules="[(val) => !!val || 'Requerido']"
                    />
                  </div>
                  <div class="col-6">
                    <q-input
                      v-model="datosTarjeta.cvv"
                      label="CVV *"
                      outlined
                      dense
                      mask="###"
                      :rules="[(val) => !!val || 'Requerido']"
                    />
                  </div>
                </div>
              </div>

              <!-- Botones de Acción -->
              <div class="row q-col-gutter-sm">
                <div class="col-6">
                  <q-btn label="Cancelar" outline color="grey" class="full-width" v-close-popup />
                </div>
                <div class="col-6">
                  <q-btn
                    label="Comprar"
                    color="primary"
                    class="full-width q-mb-md"
                    :disable="!puedeProcesarModal"
                    @click="procesarCompraModal"
                    unelevated
                  />
                </div>
              </div>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import icono_empresa from '../../assets/ventry.png'
import icono_reserva from '../../assets/reserva.jpg'
import icono_evento from '../../assets/evento.jpg'
import icono_diversion from '../../assets/diversion.jpg'
import { useQuasar } from 'quasar'
import { eventoService } from 'src/services/eventoService'
import { authService } from 'src/services/authService'

const $q = useQuasar()
// Datos del carrusel estático
const dialog = ref(false)
const formPagoModal = ref(null)
const cuponCodigo = ref('')
const cuponAplicado = ref(null)
const descuento = ref(0)
const carouselMovies = ref([
  { id: 1, title: 'Tú mejor Opción', image: icono_empresa },
  { id: 2, title: 'Aparta la fecha, nosotros te damos el evento', image: icono_reserva },
  { id: 3, title: 'Tú mejor opción en eventos', image: icono_evento },
  { id: 4, title: 'Únete a la diversión de nuestros eventos', image: icono_diversion },
])
const allEventos = ref([])
const tipoEventos = ref([])
// Lista original para Teatros


// const api = axios.create({
//   baseURL: '/api',
//   withCredentials: true,
// })
//import { imagenService } from 'src/services/imagenService'

const fetchEvents = async () => {
  try {
    $q.loading.show({ message: 'Cargando eventos desde backend...' })

    const response = await eventoService.getAllTiposEvento()
   // console.log(response)
   const responseEventos = await eventoService.getAllEventos()
    tipoEventos.value = response || [] // <-- arreglo real de eventos
    allEventos.value = responseEventos || [] // <-- arreglo real de eventos
  
  } catch (err) {
    console.error('Error al obtener eventos:', err.response?.data || err)
    $q.notify({
      type: 'negative',
      message: err.response?.data?.mensaje || 'Error al cargar los eventos',
      position: 'bottom',
      timeout: 3000,
      icon: 'error',
    })
  } finally {
    $q.loading.hide()
  }
}

// Estado compartido
const slide = ref(1)
const searchQuery = ref('')

// ===== BUSQUEDA =====
// eslint-disable-next-line no-unused-vars
const filteredEventos = computed(() => {
  if (!searchQuery.value) return allEventos.value

  return allEventos.value.filter((evento) =>
    evento.nombre.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})
const handleClear = () => {
  searchQuery.value = ''
}

// ===== MODAL DE COMPRA =====
const eventoSeleccionadoModal = ref(null)
const categoriasModal = ref([])
const carritoModal = ref([])
//const eventosParaCompra = ref([])

// Datos del cliente para la compra
const datosCliente = ref({
  nombres: authService.getUser()?.nombres || '',
  email: authService.getUser()?.email || '',
  telefono: '',
})

// Método de pago
const metodoPago = ref('tarjeta')
const metodoPagoOptions = [{ label: 'Tarjeta de Crédito', value: 'tarjeta' }]

// Datos de tarjeta
const datosTarjeta = ref({
  numero: '',
  vencimiento: '',
  cvv: '',
})

// Computed properties para el modal
const descuentoCalculado = computed(() => {
  if (!cuponAplicado.value) return 0
  return descuento.value
})
const subtotalModal = computed(() =>
  carritoModal.value.reduce((s, i) => s + i.precio_base * i.cantidad, 0)
)

const totalModal = computed(() => subtotalModal.value - descuentoCalculado.value)

const puedeProcesarModal = computed(
  () =>
    carritoModal.value.length > 0 &&
    datosCliente.value.nombres &&
    datosCliente.value.email &&
    datosCliente.value.telefono &&
    datosTarjeta.value.numero &&
    datosTarjeta.value.vencimiento &&
    datosTarjeta.value.cvv // En usuario online, SOLO tarjeta es requerida
)


// Buscador






// ===== MÉTODOS DEL MODAL DE COMPRA =====
const abrirModalCompra = async (evento) => {
  if (eventoSeleccionadoModal.value?.rowid !== evento.rowid) {
    carritoModal.value = []
    cuponAplicado.value = null
    descuento.value = 0
    cuponCodigo.value = ''
  }
  eventoSeleccionadoModal.value = evento
  await cargarCategoriasModal()
}

const cargarCategoriasModal = async () => {
  if (!eventoSeleccionadoModal.value) {
    categoriasModal.value = []
    return
  }
  try {
    categoriasModal.value = await eventoService.getCategoriasByEvento(
      eventoSeleccionadoModal.value.rowid
    )
    dialog.value = true
  } catch (error) {
    if (error.status === 403) {
      categoriasModal.value = []
      $q.notify({
        type: 'warning',
        message:
          'Aun no puedes comprar entradas para este evento. Por favor, espera a que el organizador las habilite.',
      })
      dialog.value = false
      return
    }
    $q.notify({
      type: 'negative',
      message: 'Error al cargar categorías de tickets',
    })
  }
}

const agregarAlCarritoModal = (categoria) => {
  //console.log(`Agregando al carrito: ${categoria.nombre} (ID: ${categoria.id_categoria_ticket})`)
  const existe = carritoModal.value.find((c) => c.id_categoria_ticket === categoria.id_categoria_ticket)
  if (existe) {
    existe.cantidad++
  } else {
    carritoModal.value.push({ ...categoria, cantidad: 1 })
  }
}

const eliminarDelCarritoModal = (idx) => {
  carritoModal.value.splice(idx, 1)
}

const aplicarCupon = async (idEvento) => {
  try {
    $q.loading.show({ message: 'Validando cupón...' })

    const response = await eventoService.VerficarDescuento(cuponCodigo.value, idEvento)

    cuponAplicado.value = response.codigo
    descuento.value = response.valor_descuento
    //console.log('Descuento calculado:', descuento.value)

    $q.notify({
      type: 'positive',
      message: 'Cupón aplicado correctamente',
    })
  } catch (error) {
    cuponAplicado.value = null
    descuento.value = 0
    if (error.status === 403) {
      $q.notify({
        type: 'negative',
        message: 'Cupon no válido',
      })
      return
    }
    $q.notify({
      type: 'negative',
      message: 'Cupón inválido o expirado',
    })
  } finally {
    $q.loading.hide()
  }
}

const procesarCompraModal = async () => {
  try {
    // Validar formulario
    const isValid = await formPagoModal.value.validate()
    if (!isValid) {
      $q.notify({
        type: 'warning',
        message: 'Por favor completa todos los campos requeridos',
      })
      return
    }

    // Mostrar dialog de procesamiento
    $q.loading.show({ message: 'Procesando tu compra...' })

    // Preparar datos de la compra
    // En pagina-principal-usuario SOLO se acepta TARJETA DE CRÉDITO (compra online)
    const compraData = {
      usuario_id: 1,
      numero_factura: 'NC' + Math.floor(1000 + Math.random() * 9000),
      evento_id: eventoSeleccionadoModal.value.rowid,
      subtotal: subtotalModal.value,
      total_descuentos: descuentoCalculado.value,
      total_pagar: totalModal.value,
      id_transaccion_pasarela: 'PROCESADA',
      metodo_pago: 'TARJETA', 
    }
    const pagoTarjeta = {
      monto: totalModal.value,
      cuenta_origen: datosTarjeta.value.numero,
    }

    const responsePasarela = await eventoService.pasarelaPago(pagoTarjeta)

    if (!responsePasarela) {
      $q.notify({
        type: 'negative',
        message: 'Pago rechazado, intente nuevamente ',
        //caption: `Compra: ${compraData.numeroCompra}`,
        timeout: 4000,
        position: 'bottom',
      })
      return
    }

    const responseCompra = await eventoService.createCompra(compraData)

    const items = carritoModal.value.map((item) => ({
      compra_id: responseCompra.id,
      categoria_ticket_id: item.id_categoria_ticket,
      //categoriaNombre: item.nombre,
      cantidad: item.cantidad,
      precio_unitario: item.precio_base,
      subtotal: item.precio_base * item.cantidad,
    }))
    //console.log(items)
    await eventoService.DetalleCompra(items)

    // Mostrar éxito
    $q.notify({
      type: 'positive',
      message: '¡Compra procesada exitosamente!',
      //caption: `Compra: ${compraData.numeroCompra}`,
      timeout: 4000,
      position: 'bottom',
    })


    // Limpiar modal
    dialog.value = false
    carritoModal.value = []
    eventoSeleccionadoModal.value = null
    datosTarjeta.value = { numero: '', vencimiento: '', cvv: '' }
  } catch (error) {
    $q.loading.hide()
    console.error('Error procesando compra:', error)
    $q.notify({
      type: 'negative',
      message: error.response?.data?.message || 'Error al procesar la compra',
    })
  } finally {
    $q.loading.hide()
  }
}



// Verificar si se necesita cargar más eventos al montar
onMounted(async () => {
  await fetchEvents()
})
</script>

<style scoped>
/* ====== GENERAL ====== */
.main-page {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: calc(100vh - 50px);
  padding-bottom: 80px;
}

/* ====== HERO SECTION ====== */
.hero-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
  border-radius: 0;
}

.carousel-banner {
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.carousel-slide-custom {
  position: relative;
}

.carousel-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.4) 0%, rgba(118, 75, 162, 0.4) 100%);
  z-index: 1;
}

.carousel-content {
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
}

.carousel-title {
  text-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  animation: fadeInDown 0.8s ease;
}

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ====== SEARCH SECTION ====== */
.search-section {
  background: white;
  padding: 30px 20px;
  border-radius: 16px;
  margin: -40px 20px 0;
  position: relative;
  z-index: 10;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.container {
  max-width: 800px;
  margin: 0 auto;
}

.search-input {
  background: white !important;
  border-radius: 12px !important;
  font-size: 16px;
}

.search-input input {
  font-size: 16px;
}

/* ====== EVENTS SECTION ====== */
.events-section {
  padding: 0 20px;
  max-width: 1400px;
  margin: 0 auto;
}

.section-header {
  display: flex;
  align-items: center;
  margin-bottom: 30px;
  padding-top: 20px;
}

.section-title {
  font-size: 32px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
  letter-spacing: -0.5px;
}

/* ====== EVENTS SCROLL CONTAINER ====== */
.events-scroll-container {
  display: flex;
  flex-wrap: nowrap;
  overflow-x: auto;
  gap: 24px;
  padding-bottom: 20px;
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
}

.events-scroll-container::-webkit-scrollbar {
  height: 8px;
}

.events-scroll-container::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.events-scroll-container::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 10px;
}

.events-scroll-container::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #5568d3 0%, #6a3a8f 100%);
}

/* ====== EVENT CARD ====== */
.event-card-wrapper {
  flex: 0 0 calc(25% - 18px);
  min-width: 280px;
}

@media (max-width: 1200px) {
  .event-card-wrapper {
    flex: 0 0 calc(33.333% - 16px);
  }
}

@media (max-width: 768px) {
  .event-card-wrapper {
    flex: 0 0 calc(50% - 12px);
  }
}

@media (max-width: 480px) {
  .event-card-wrapper {
    flex: 0 0 calc(100% - 0px);
  }
}

.event-card {
  height: 100%;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  transform: translateY(0);
}

.event-card:hover {
  transform: translateY(-12px);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
}

.event-image-container {
  position: relative;
  overflow: hidden;
  height: 200px;
}

.event-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.event-card:hover .event-image {
  transform: scale(1.08);
}

.event-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.2) 100%);
  transition: all 0.3s ease;
}

.event-card:hover .event-overlay {
  background: linear-gradient(180deg, rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.4) 100%);
}

.event-card-content {
  padding: 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  height: calc(100% - 200px);
  background: white;
}

.event-title {
  font-size: 18px;
  font-weight: 700;
  color: #2c3e50;
  margin: 0;
  line-height: 1.3;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.event-btn {
  border-radius: 8px !important;
  font-weight: 600;
  transition: all 0.3s ease !important;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 13px;
}

.event-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4) !important;
}

/* ====== EMPTY STATE ====== */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  border-radius: 16px;
  min-height: 300px;
  text-align: center;
}

.empty-state p {
  font-size: 16px;
  color: #7f8c8d;
}

/* ====== MODAL DE COMPRA ====== */
.purchase-modal {
  min-width: 900px;
  max-width: 95vw;
  border-radius: 16px;
  overflow: hidden;
}
.modal-scroll {
  max-height: 80vh;
  overflow-y: auto;
}
.categoria-item {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.categoria-item:hover {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  transform: translateX(8px);
}

.carrito-section {
  border: 2px solid #ecf0f1;
  border-radius: 12px;
  background: linear-gradient(135deg, #f8f9fa 0%, #f0f3f7 100%);
}

.hover-effect {
  cursor: pointer;
  transition: all 0.2s ease;
}

.hover-effect:hover {
  transform: translateX(4px);
}

/* ====== RESPONSIVE ====== */
@media (max-width: 768px) {
  .main-page {
    padding: 0 !important;
  }

  .hero-section {
    padding: 15px;
  }

  .search-section {
    margin: -30px 15px 0;
    padding: 20px 15px;
  }

  .section-title {
    font-size: 24px;
  }

  .event-card-wrapper {
    flex: 0 0 calc(50% - 12px);
  }

  .events-section {
    padding: 0 15px;
  }
}

@media (max-width: 480px) {
  .hero-section {
    padding: 10px;
  }

  .section-title {
    font-size: 20px;
  }

  .carousel-title {
    font-size: 20px !important;
  }

  .event-card-wrapper {
    flex: 0 0 100%;
  }

  .search-input input {
    font-size: 14px;
  }
}
</style>
