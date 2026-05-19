<template>
  <div class="q-pa-md">
    <q-table
      title="Clientes"
      :rows="rows"
      :columns="columns"
      row-key="rowid"
      :filter="filter"
      :loading="loading"
    >
      <template v-slot:top-right>
        <q-input dense debounce="300" v-model="filter" placeholder="Buscar">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
        <q-btn color="primary" label="Nuevo Cliente" @click="openDialog()" class="q-ml-md" />
      </template>

      <template v-slot:body-cell-acciones="props">
        <q-td :props="props">
          <q-btn flat round dense color="primary" icon="edit" @click="editarCliente(props.row)" />
          <q-btn flat round dense color="negative" icon="delete" @click="eliminarCliente(props.row)" />
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="dialog" persistent>
      <q-card style="min-width: 600px">
        <q-card-section>
          <div class="text-h6">{{ modoEdicion ? 'Editar Cliente' : 'Nuevo Cliente' }}</div>
        </q-card-section>

        <q-card-section>
          <q-form ref="formRef">
            <q-input
              v-model="form.nombres"
              label="Ingresa tu nombre *"
              :rules="[val => !!val || 'Campo requerido']"
            />
            <q-input
              v-model="form.apellidos"
              label="Ingresa tus apellidos *"
              :rules="[val => !!val || 'Campo requerido']"
            />
            <q-input
              v-model="form.email"
              label="Email *"
              type="email"
              :rules="[val => !!val || 'Campo requerido']"
            />
            <q-input
              v-model="form.telefono"
              label="Teléfono *"
              mask="####-####"
              :rules="[val => !!val || 'Campo requerido']"
            />
            <q-input
              v-model="form.dui"
              label="DUI"
              mask="########-#"
            />
            <q-select
              v-model="form.estado"
              :options="['ACTIVO', 'INACTIVO']"
              label="Estado *"
            />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" color="primary" v-close-popup @click="resetForm" />
          <q-btn label="Guardar" color="primary" @click="guardar" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { clienteService } from 'src/services/clienteService'
const formRef = ref(null)
const $q = useQuasar()
const rows = ref([])
const filter = ref('')
const loading = ref(false)
const dialog = ref(false)
const modoEdicion = ref(false)
const clienteId = ref(null)

const form = ref({
  nombres: '',
  apellidos: '',
  email: '',
  telefono: '',
  dui: '',
  estado: 'ACTIVO'
})

const columns = [
  { name: 'rowid', label: 'ID', field: 'rowid', align: 'left', sortable: true },
  { name: 'nombre', label: 'Nombre', field: 'nombres', align: 'left', sortable: true },
  { name: 'apellido', label: 'Apellidos', field: 'apellidos', align: 'left', sortable: true },
  { name: 'email', label: 'Email', field: 'email', align: 'left', sortable: true },
  { name: 'telefono', label: 'Teléfono', field: 'telefono', align: 'left' },
  { name: 'dui', label: 'DUI', field: 'dui', align: 'left' },
  { name: 'estado', label: 'Estado', field: 'estado', align: 'center' },
  { name: 'acciones', label: 'Acciones', align: 'center' }
]

const cargarClientes = async () => {
  loading.value = true
  try {
    rows.value = await clienteService.getAllClientes()
    //console.log(rows.value)
  } catch {
    $q.notify({ type: 'negative', message: 'Error al cargar clientes' })
  } finally {
    loading.value = false
  }
}

const openDialog = () => {
  resetForm()
  dialog.value = true
}

const editarCliente = (row) => {
  modoEdicion.value = true
  clienteId.value = row.rowid
  form.value = { ...row }
  dialog.value = true
}

const eliminarCliente = (row) => {
  $q.dialog({
    title: 'Confirmar',
    message: `¿Eliminar cliente ${row.nombre}?`,
    cancel: true
  }).onOk(async () => {
    try {
      await clienteService.deleteCliente(row.rowid)
      $q.notify({ type: 'positive', message: 'Cliente eliminado' })
      cargarClientes()
    } catch {
      $q.notify({ type: 'negative', message: 'Error al eliminar' })
    }
  })
}

const guardar = async () => {
   const esValido = await formRef.value.validate()
  if (!esValido) {
    $q.notify({ type: 'warning', message: 'Por favor, completa los campos requeridos' })
    return
  }
  try {
    if (modoEdicion.value) {
      await clienteService.updateCliente(clienteId.value, form.value)
      $q.notify({ type: 'positive', message: 'Cliente actualizado' })
    } else {
      await clienteService.createCliente(form.value)
      $q.notify({ type: 'positive', message: 'Cliente creado' })
    }
    dialog.value = false
    cargarClientes()
  } catch {
    $q.notify({ type: 'negative', message: 'Error al guardar' })
  }
}

const resetForm = () => {
  modoEdicion.value = false
  clienteId.value = null
  form.value = {
    nombre: '',
    email: '',
    telefono: '',
    dui: '',
    estado: 'ACTIVO'
  }
}

onMounted(() => {
  cargarClientes()
})
</script>
