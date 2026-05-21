<template>
  <div class="q-pa-md">
    <q-btn label="Añadir Evento" color="secondary" @click="openDialog()" />

    <q-dialog v-model="dialog" @hide="modoEdicion = false">
      <q-card style="min-width: 50vw; min-height: 60vh">
        <q-card-section class="row items-center q-pb-none text-h6"> Nuevo Evento </q-card-section>

        <q-card-section>
          <q-stepper v-model="step" vertical color="purple" animated>
            <q-step :name="1" title="Datos generales del evento" icon="settings" :done="step > 1">
              <q-input filled v-model="nombreEvento" label="Nombre del evento *" lazy-rules
                :rules="[(val) => (val && val.length > 0) || 'Indica este campo']" />
              <q-input v-model="descripcion" type="textarea" label="Descripción del evento"
                placeholder="Detalla la información del evento" filled :rows="3" maxlength="200"
                :rules="[(val) => (val && val.length > 0) || 'Indica este campo']" />
              <q-select filled v-model="estado" :options="options" label="Estado del evento"
                :rules="[(val) => !!val || 'Indica este campo']" />
              <q-select filled v-model="tipoEvento" :options="tipoEventoOptions" label="Tipo de evento"
                :rules="[(val) => !!val || 'Indica este campo']" />
              <q-uploader accept="image/*" max-files="1" :max-total-size="5 * 1024 * 1024"
                :max-file-size="5 * 1024 * 1024" label="Agrega foto publicitaria" color="purple" icon="photo_camera"
                @added="onFileAdded" @failed="onFileFailed" />
              <div v-if="modoEdicion && archivosSubidos.length === 0" class="q-mt-md">
                <label class="text-subtitle2">Foto actual del evento:</label><br />
                <img :src="'http://localhost:8000' + rowPhoto || icono_diversion" alt="Foto actual"
                  style="width: 150px; height: 150px; object-fit: cover; border-radius: 5px" />
              </div>
              <q-stepper-navigation>
                <q-btn @click="validateStep1" color="purple" label="Continuar" :disable="!isStep1Valid" />
              </q-stepper-navigation>
            </q-step>

            <q-step :name="2" title="Ubicación y fecha del evento" caption="Optional" icon="event" :done="step > 2">
              <q-select filled v-model="lugarEvento" :options="lugares" label="Ubicación del evento"
                :rules="[(val) => !!val || 'Indica este campo']" />
              <label class="text-subtitle2 q-mb-xs" style="display: block; margin-bottom: 4px">
                Indica fecha y hora del evento
              </label>
              <div class="q-gutter-sm row items-start">
                <q-date v-model="fechaEvento" mask="YYYY-MM-DD" color="purple"
                  :rules="[(val) => (val && val.length > 0) || 'Indica la fecha']" />
                <q-time v-model="horaEvento" mask="HH:mm" format24h color="purple"
                  :rules="[(val) => !!val || 'Indica la hora']" />
              </div>
              <q-stepper-navigation>
                <q-btn @click="validateStep2" color="purple" label="Continuar" :disable="!isStep2Valid" />
                <q-btn flat @click="step = 1" color="purple" label="Regresar" class="q-ml-sm" />
              </q-stepper-navigation>
            </q-step>

            <q-step :name="3" title="Información de venta" icon="monetization_on">
              <q-banner v-if="modoEdicion" class="bg-grey-2 text-grey-9 q-mb-md" dense>
                <q-icon name="info" class="q-mr-sm" />
                Modifica las localidades existentes o desmárcalas para eliminarlas.
              </q-banner>
              <label v-if="!modoEdicion" class="text-subtitle2 q-mb-xs" style="display: block; margin-bottom: 4px">
                Localidades disponibles
              </label>
              <div class="q-gutter-sm">
                <div v-for="(ticket, index) in ticketTypes" :key="index" class="row items-center q-mb-sm">
                  <q-checkbox v-model="ticket.selected" :label="ticket.nombre" style="width: 120px" />
                  <q-input v-model.number="ticket.price" type="number" :label="'Precio ' + (index + 1)" class="q-mx-sm"
                    dense outlined :disable="!ticket.selected" style="width: 120px"
                    :rules="[(val) => !ticket.selected || (val !== null && val !== '' && val >= 0) || 'Indica un precio válido']" />
                  <q-input v-model.number="ticket.quantity" type="number" :label="'Cantidad ' + (index + 1)" dense
                    outlined :disable="!ticket.selected" style="width: 120px"
                    :rules="[(val) => !ticket.selected || (val !== null && val !== '' && val > 0) || 'Indica una cantidad válida']" />
                  <span v-if="ticket.selected && ticket.price" class="text-caption text-green-8 q-ml-sm">
                    +IVA: ${{ (ticket.price * 0.13).toFixed(2) }} → Total: ${{ (ticket.price * 1.13).toFixed(2) }}
                  </span>
                </div>
                <div class="q-mt-md bg-blue-1 q-pa-sm rounded-borders">
                  <q-icon name="info" color="blue" class="q-mr-sm" />
                  <span class="text-caption text-blue-8">IVA (13%) se aplica automáticamente al precio de cada localidad.</span>
                </div>
              </div>
              <q-stepper-navigation>
                <q-btn @click="onSubmit" color="purple" :label="modoEdicion ? 'Editar' : 'Guardar'" />
                <q-btn flat @click="onReset" color="purple" label="Reiniciar" class="q-ml-sm" />
                <q-btn flat @click="step = 2" color="purple" label="Regresar" class="q-ml-sm" />
              </q-stepper-navigation>
            </q-step>
          </q-stepper>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Close" color="black" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-table flat bordered title="Datos de cada evento" :rows="rows" :columns="columns" row-key="name"
      :visible-columns="['fotoEvento', 'nombreEvento', 'fecha-hora', 'capacidad', 'estado', 'accion']">
      <template v-slot:header="props">
        <q-tr :props="props">
          <q-th auto-width />
          <q-th v-for="col in props.cols" :key="col.name" :props="props">{{ col.label }}</q-th>
        </q-tr>
      </template>
      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td auto-width>
            <q-btn size="sm" color="accent" round dense @click="props.expand = !props.expand"
              :icon="props.expand ? 'remove' : 'add'" />
          </q-td>
          <q-td v-for="col in props.cols" :key="col.name" :props="props">
            <template v-if="col.name === 'fotoEvento'">
              <img :src="props.row.photo" alt="Ícono del evento" style="width: 50px; height: 50px; object-fit: cover" />
            </template>
            <template v-else-if="col.name === 'accion'">
              <q-btn flat dense color="primary" icon="edit" label="Editar" @click="editarEvento(props.row)"
                class="q-mr-sm acciones" />
              <q-btn flat dense color="negative" icon="delete" label="Eliminar" @click="eliminarEvento(props.row)"
                class="q-mr-sm acciones" />
            </template>
            <template v-else>{{ col.value }}</template>
          </q-td>
        </q-tr>
        <q-tr v-show="props.expand" :props="props">
          <q-td colspan="100%">
            <div class="text-left">{{ props.row.description }}</div>
          </q-td>
        </q-tr>
      </template>
    </q-table>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import icono_diversion from '../../assets/diversion.jpg'
import { eventoService } from 'src/services/eventoService'
import { imagenService } from 'src/services/imagenService'

const $q = useQuasar()
const step = ref(1)
const nombreEvento = ref('')
const descripcion = ref('')
const estado = ref(null)
const tipoEvento = ref(null)
const lugarEvento = ref('')
const fechaEvento = ref('')
const horaEvento = ref('')
const archivosSubidos = ref([])
const dialog = ref(false)
const modoEdicion = ref(false)
const options = ['ACTIVO', 'CANCELADO', 'INACTIVO']
const tipoEventoOptions = [
  { label: 'Concierto', value: 1 },
  { label: 'Teatro', value: 2 },
  { label: 'Festival', value: 3 },
  { label: 'Deportivo', value: 4 },
  { label: 'Conferencia', value: 5 },
  { label: 'Exposición', value: 6 },
]
const lugares = ref([])
const EventoId = ref(null)
const ticketTypes = ref([
  { id: 1, nombre: 'General', selected: false, price: null, quantity: null },
  { id: 2, nombre: 'VIP', selected: false, price: null, quantity: null },
  { id: 3, nombre: 'Platinum', selected: false, price: null, quantity: null },
  { id: 4, nombre: 'Platea', selected: false, price: null, quantity: null },
  { id: 5, nombre: 'Palco', selected: false, price: null, quantity: null },
  { id: 6, nombre: 'Entrada General', selected: false, price: null, quantity: null },
  { id: 7, nombre: 'All Access', selected: false, price: null, quantity: null },
])
const rowPhoto = ref(null)
const IVA = 0.13

onMounted(() => {
  mostrarEventos()
  ListaLugares()
})

const columns = [
  { name: 'fotoEvento', label: 'Ícono', field: 'photo', align: 'center' },
  { name: 'nombreEvento', required: true, label: 'Nombre del evento', align: 'left', field: (row) => row.eventName, format: (val) => `${val}`, sortable: true },
  { name: 'tipoEvento', label: 'Tipo evento', field: 'type', align: 'center', sortable: true },
  { name: 'lugarEvento', label: 'Lugar evento', field: 'place', align: 'center', sortable: true },
  { name: 'fecha-hora', label: 'Fecha/Hora evento', align: 'center', field: 'date', format: (val) => new Date(val).toLocaleString(), sortable: true },
  { name: 'capacidad', label: 'Aforo', field: 'capacity', align: 'center', sortable: true },
  { name: 'estado', label: 'Estado del evento', align: 'center', field: 'state', sortable: true },
  { name: 'accion', label: 'Acciones', align: 'center' },
]
const rows = ref([])

const ListaLugares = async () => {
  try {
    const response = await eventoService.getAllLugares()
    lugares.value = response.map((l) => ({ label: l.nombre, value: l.rowid }))
  } catch (err) {
    console.error('Error al obtener lugares:', err)
  }
}

const mostrarEventos = async () => {
  try {
    $q.loading.show({ message: 'Cargando eventos...' })
    const eventos = await eventoService.getAllEventos()
    rows.value = eventos.map((event) => ({
      name: event.rowid,
      eventName: event.nombre,
      photo: imagenService.getUrl(event.fotoPublicitaria, icono_diversion),
      description: event.descripcion,
      type: event.tipoEvento,
      place: event.lugarEvento,
      date: event.fechaProgramado,
      capacity: event.capacidadTotal,
      state: event.estado,
    }))
  } catch (err) {
    console.error('Error al obtener eventos:', err.response?.data)
    $q.notify({ type: 'negative', message: 'Error al cargar los eventos', position: 'bottom', timeout: 3000 })
  } finally {
    $q.loading.hide()
  }
}

const isStep1Valid = computed(() => {
  return nombreEvento.value && descripcion.value && estado.value !== null
})

const isStep2Valid = computed(() => {
  return lugarEvento.value !== null && fechaEvento.value && horaEvento.value
})

const validateStep1 = () => {
  if (isStep1Valid.value) {
    step.value = 2
  } else {
    $q.notify({ message: 'Por favor, completa todos los campos requeridos', color: 'negative', position: 'top' })
  }
}

const validateStep2 = () => {
  if (isStep2Valid.value) {
    step.value = 3
  } else {
    $q.notify({ message: 'Por favor, completa todos los campos requeridos', color: 'negative', position: 'top' })
  }
}

const onFileAdded = (files) => { archivosSubidos.value = files }
const onFileFailed = () => { $q.notify({ message: 'Error al subir la foto', color: 'negative', position: 'top' }) }

const openDialog = () => {
  if (!modoEdicion.value) onReset()
  dialog.value = true
}

const editarCategorias = async (idEvento) => {
  try {
    return await eventoService.getCategoriasByEvento(idEvento)
  } catch {
    return []
  }
}

const editarEvento = async (row) => {
  try {
    modoEdicion.value = true
    EventoId.value = row.name
    archivosSubidos.value = []
    ticketTypes.value.forEach((ticket) => {
      ticket.selected = false
      ticket.price = null
      ticket.quantity = null
      ticket.idCategoriaEvento = null
    })
    nombreEvento.value = row.eventName
    descripcion.value = row.description
    estado.value = row.state
    lugarEvento.value = lugares.value.find((l) => l.value === row.place) || null
    tipoEvento.value = tipoEventoOptions.find((t) => t.value === row.type) || null
    if (row.date) {
      const fechaISO = new Date(row.date)
      fechaEvento.value = fechaISO.toISOString().split('T')[0]
      horaEvento.value = fechaISO.toTimeString().substring(0, 5)
    }
    rowPhoto.value = row.photo.replace(/^https?:\/\/[^/]+/, '')
    console.log(row.name)
    const categorias = await editarCategorias(row.name)
    console.log(categorias.value)
    if (categorias && categorias.length > 0) {
      ticketTypes.value.forEach((ticket) => {
        const existente = categorias.find((c) => c.nombre === ticket.nombre)
        if (existente) {
          ticket.selected = true
          ticket.price = existente.precioBase
          ticket.quantity = existente.capacidadDisponible
          ticket.idCategoriaEvento = existente.rowid
        }
      })
    }
    openDialog('editar')
  } catch (error) {
    console.error('Error al editar evento:', error)
  }
}

const eliminarEvento = (row) => {
  $q.dialog({
    title: 'Confirmar Eliminación',
    message: `¿Eliminar el evento ${row.eventName}?`,
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    try {
      const categorias = await editarCategorias(row.name)
      if (categorias && categorias.length > 0) {
        for (const cat of categorias) {
          if (cat.rowid || cat.id_categoria_ticket) {
            await eventoService.deleteCategoria(cat.rowid ?? cat.id_categoria_ticket).catch(() => {})
          }
        }
      }
      /*await eventoService.deleteFotoEvento(row.name).catch(() => {})*/
      await eventoService.deleteEvento(row.name)
      $q.notify({ message: `Evento ${row.eventName} eliminado`, color: 'negative', position: 'top' })
      mostrarEventos()
    } catch (error) {
      $q.notify({ message: error.response?.data?.message || 'Error al eliminar el evento', color: 'warning', position: 'top' })
    }
  })
}

const onSubmit = async () => {
  const categoriasSeleccionadas = ticketTypes.value.filter(t => t.selected)
  if (categoriasSeleccionadas.length === 0) {
    $q.notify({ message: 'Debes seleccionar al menos una categoría', color: 'warning', position: 'top' })
    return
  }
  const categoriasInvalidas = categoriasSeleccionadas.filter(t =>
    t.price == null || t.price === '' || t.quantity == null || t.quantity === ''
  )
  if (categoriasInvalidas.length > 0) {
    $q.notify({ message: 'Todas las categorías seleccionadas deben tener precio y cantidad', color: 'warning', position: 'top' })
    return
  }

  const eventoData = {
    nombre: nombreEvento.value,
    descripcion: descripcion.value,
    tipo_evento_id: tipoEvento.value.value,
    lugar_evento_id: lugarEvento.value.value,
    fecha_programado: `${fechaEvento.value}T${horaEvento.value}:00`,
    capacidad_total: ticketTypes.value.reduce((acc, t) => acc + (t.quantity || 0), 0),
    estado: estado.value.toUpperCase(),
  }

  try {
    if (modoEdicion.value) {
      await eventoService.updateEvento(EventoId.value, eventoData)
      if (archivosSubidos.value.length > 0) {
        try {
          await eventoService.updateFotoEvento(EventoId.value, archivosSubidos.value[0])
        } catch {
          // foto opcional
        }
      }
      for (const cat of ticketTypes.value) {
        if (cat.selected && cat.idCategoriaEvento) {
          await eventoService.updateCategoria(cat.idCategoriaEvento, {
            precioBase: parseFloat((cat.price * (1 + IVA)).toFixed(2)),
            capacidadDisponible: cat.quantity,
            estado: 'ACTIVO',
            nombre: cat.nombre,
            descripcion: 'Categoría de ticket',
          })
        } else if (cat.selected && !cat.idCategoriaEvento) {
          await eventoService.createCategoria({
            precioBase: parseFloat((cat.price * (1 + IVA)).toFixed(2)),
            capacidadDisponible: cat.quantity,
            eventoId: EventoId.value,
            nombre: cat.nombre,
            descripcion: 'Categoría de ticket',
          })
        } else if (!cat.selected && cat.idCategoriaEvento) {
          await eventoService.deleteCategoria(cat.idCategoriaEvento)
        }
      }
      $q.notify({ message: 'Evento actualizado correctamente', color: 'positive', position: 'top' })
    } else {
      const response = await eventoService.createEvento(eventoData)
      const nuevoEventoId = response.rowid
      if (archivosSubidos.value.length > 0) {
        await eventoService.uploadFotoEvento(nuevoEventoId, archivosSubidos.value[0])
      }
      for (const cat of ticketTypes.value) {
        if (cat.selected) {
          await eventoService.createCategoria({
            precioBase: parseFloat((cat.price * (1 + IVA)).toFixed(2)),
            capacidadDisponible: cat.quantity,
            eventoId: nuevoEventoId,
            nombre: cat.nombre,
            descripcion: 'Categoría de ticket',
          })
        }
      }
      $q.notify({ message: `Evento ${nombreEvento.value} agregado`, color: 'positive', position: 'top' })
    }
    dialog.value = false
    onReset()
    mostrarEventos()
  } catch (error) {
    console.error('Error en submit:', error.response?.data)
    $q.notify({ message: error.response?.data?.message || 'Error al guardar el evento', color: 'negative', position: 'top' })
  }
}

const onReset = () => {
  nombreEvento.value = ''
  descripcion.value = ''
  estado.value = null
  tipoEvento.value = null
  lugarEvento.value = null
  ticketTypes.value.forEach((ticket) => {
    ticket.selected = false
    ticket.price = null
    ticket.quantity = null
    ticket.idCategoriaEvento = null
  })
  fechaEvento.value = ''
  horaEvento.value = ''
  archivosSubidos.value = []
  step.value = 1
}
</script>

<style scoped>
.q-table th { font-weight: bold; }
.q-table img { border-radius: 5px; }
.q-table { border-radius: 10px; }
.acciones { margin: 0 4px; width: 50px; height: 30px; font-size: 12px; }
.text-subtitle2 { font-size: 14px; font-weight: 500; color: #555; }
</style>