<template>
  <div class="q-pa-md">
    <q-table
      title="Tipos de Evento"
      :rows="rows"
      :columns="columns"
      row-key="id_tipo_evento"
    >
      <template v-slot:top-right>
        <q-btn color="primary" label="Nuevo Tipo" @click="openDialog()" />
      </template>

      <template v-slot:body-cell-acciones="props">
        <q-td :props="props">
          <q-btn flat round dense color="primary" icon="edit" @click="editar(props.row)" />
          <q-btn flat round dense color="negative" icon="delete" @click="eliminar(props.row)" />
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="dialog" persistent>
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">{{ modoEdicion ? 'Editar' : 'Nuevo' }} Tipo de Evento</div>
        </q-card-section>
        <q-card-section>
          <q-input v-model="form.nombre" label="Nombre *" />
          <q-select v-model="form.estado" :options="['ACTIVO', 'INACTIVO']" label="Estado *" />
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
const rows = ref([])
const dialog = ref(false)
const modoEdicion = ref(false)
const tipoId = ref(null)

const form = ref({
  nombre: '',
  estado: 'ACTIVO'
})

const columns = [
  { name: 'nombre', label: 'Nombre', field: 'nombre', align: 'left', sortable: true },
  { name: 'estado', label: 'Estado', field: 'estado', align: 'center' },
  { name: 'acciones', label: 'Acciones', align: 'center' }
]

const cargarTipos = async () => {
  try {
    rows.value = await eventoService.getAllTiposEvento()
  } catch {
    $q.notify({ type: 'negative', message: 'Error al cargar tipos' })
  }
}

const openDialog = () => {
  resetForm()
  dialog.value = true
}

const editar = (row) => {
  modoEdicion.value = true
  tipoId.value = row.id_tipo_evento
  form.value = { ...row }
  dialog.value = true
}

const eliminar = (row) => {
  $q.dialog({
    title: 'Confirmar',
    message: `¿Eliminar tipo ${row.nombre}?`,
    cancel: true
  }).onOk(async () => {
    try {
      await eventoService.deleteTipoEvento(row.id_tipo_evento)
      $q.notify({ type: 'positive', message: 'Tipo eliminado' })
      cargarTipos()
    } catch {
      $q.notify({ type: 'negative', message: 'Error al eliminar' })
    }
  })
}

const guardar = async () => {
  try {
    if (modoEdicion.value) {
      await eventoService.updateTipoEvento(tipoId.value, form.value)
      $q.notify({ type: 'positive', message: 'Tipo actualizado' })
    } else {
      console.log(form.value)
      await eventoService.createTipoEvento(form.value)

      $q.notify({ type: 'positive', message: 'Tipo creado' })
    }
    dialog.value = false
    cargarTipos()
  } catch (error) {
    $q.notify({ type: 'negative', message: 'Error al guardar' })
    console.log(error)
  }
}

const resetForm = () => {
  modoEdicion.value = false
  tipoId.value = null
  form.value = {
    nombre: '',
    estado: 'ACTIVO'
  }
}

onMounted(() => {
  cargarTipos()
})
</script>
