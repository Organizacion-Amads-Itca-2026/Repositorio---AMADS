<template>
  <div class="q-pa-md">
    <q-card class="q-mb-md">
      <q-card-section>
        <q-select
          v-model="eventoSeleccionado"
          :options="eventos"
          option-label="nombre"
          label="Selecciona un Evento"
          @update:model-value="cargarCategorias"
        />
      </q-card-section>
    </q-card>

    <q-table
      v-if="eventoSeleccionado"
      title="Categorías de Tickets"
      :rows="rows"
      :columns="columns"
      row-key="id_categoria_ticket"
    >
      <template v-slot:top-right>
        <q-btn color="primary" label="Nueva Categoría" @click="openDialog()" />
      </template>

      <template v-slot:body-cell-precioBase="props">
        <q-td :props="props">
          ${{ props.row.precioBase }}
        </q-td>
      </template>

      <template v-slot:body-cell-acciones="props">
        <q-td :props="props">
          <q-btn flat round dense color="primary" icon="edit" @click="editar(props.row)" />
          <q-btn flat round dense color="negative" icon="delete" @click="eliminar(props.row)" />
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="dialog" persistent>
      <q-card style="min-width: 500px">
        <q-card-section>
          <div class="text-h6">{{ modoEdicion ? 'Editar' : 'Nueva' }} Categoría</div>
        </q-card-section>

        <q-card-section>
          <q-input v-model="form.nombre" label="Nombre de la Categoría *" />

          <q-input
            v-model="form.descripcion"
            label="Descripción"
            type="textarea"
            rows="2"
          />

          <q-input
            v-model.number="form.precioBase"
            label="Precio Base *"
            type="number"
            prefix="$"
            step="0.01"
          />

          <q-input
            v-model.number="form.capacidadDisponible"
            label="Capacidad Disponible *"
            type="number"
          />

          <q-select
            v-model="form.estado"
            :options="['ACTIVO', 'INACTIVO', 'AGOTADO']"
            label="Estado *"
          />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" v-close-popup @click="resetForm" />
          <q-btn label="Guardar" color="primary" @click="guardar" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { eventoService } from 'src/services/eventoService'

const $q = useQuasar()
const eventos = ref([])
const eventoSeleccionado = ref(null)
const rows = ref([])
const dialog = ref(false)
const modoEdicion = ref(false)
const categoriaId = ref(null)

const form = ref({
  eventoId: null,
  nombre: '',
  descripcion: '',
  precioBase: 0,
  capacidadDisponible: 0,
  estado: 'ACTIVO'
})

const columns = [
  { name: 'nombre', label: 'Categoría', field: 'nombre', align: 'left', sortable: true },
  { name: 'descripcion', label: 'Descripción', field: 'descripcion', align: 'left' },
  { name: 'precioBase', label: 'Precio', align: 'right', sortable: true },
  { name: 'capacidadDisponible', label: 'Disponibles', field: 'capacidadDisponible', align: 'center' },
  { name: 'estado', label: 'Estado', field: 'estado', align: 'center' },
  { name: 'acciones', label: 'Acciones', align: 'center' }
]

const cargarEventos = async () => {
  try {
    eventos.value = await eventoService.getAllEventos()
  } catch {
    $q.notify({ type: 'negative', message: 'Error al cargar eventos' })
  }
}

const cargarCategorias = async () => {
  if (!eventoSeleccionado.value) return
  try {
    rows.value = await eventoService.getCategoriasByEvento(eventoSeleccionado.value.rowid)
  } catch {
    $q.notify({ type: 'negative', message: 'Error al cargar categorías' })
  }
}

const openDialog = () => {
  if (!eventoSeleccionado.value) {
    $q.notify({ type: 'warning', message: 'Selecciona un evento primero' })
    return
  }
  resetForm()
  dialog.value = true
}

const editar = (row) => {
  modoEdicion.value = true
  categoriaId.value = row.id_categoria_ticket
  form.value = { ...row }
  dialog.value = true
}

const eliminar = (row) => {
  $q.dialog({
    title: 'Confirmar',
    message: `¿Eliminar categoría ${row.nombre}?`,
    cancel: true
  }).onOk(async () => {
    try {
      await eventoService.deleteCategoria(row.id_categoria_ticket)
      $q.notify({ type: 'positive', message: 'Categoría eliminada' })
      cargarCategorias()
    } catch {
      $q.notify({ type: 'negative', message: 'Error al eliminar' })
    }
  })
}

const guardar = async () => {
  try {
    const data = { ...form.value, eventoId: eventoSeleccionado.value.rowid }

    if (modoEdicion.value) {
      await eventoService.updateCategoria(categoriaId.value, data)
      $q.notify({ type: 'positive', message: 'Categoría actualizada' })
    } else {
      await eventoService.createCategoria(data)
      $q.notify({ type: 'positive', message: 'Categoría creada' })
    }
    dialog.value = false
    cargarCategorias()
  } catch {
    $q.notify({ type: 'negative', message: 'Error al guardar' })
  }
}

const resetForm = () => {
  modoEdicion.value = false
  categoriaId.value = null
  form.value = {
    eventoId: null,
    nombre: '',
    descripcion: '',
    precioBase: 0,
    capacidadDisponible: 0,
    estado: 'ACTIVO'
  }
}

onMounted(() => {
  cargarEventos()
})
</script>
