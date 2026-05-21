<template>
  <div class="q-pa-md">
    <q-table
      title="Usuarios del Sistema"
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
      </template>

      <template v-slot:body-cell-estado="props">
        <q-td :props="props">
          <q-badge :color="props.row.estado === 'ACTIVO' ? 'positive' : 'grey'">
            {{ props.row.estado }}
          </q-badge>
        </q-td>
      </template>

<template v-slot:body-cell-perfil="props">
        <q-td :props="props">
            {{ obtenerNombrePerfil(props.row.perfil) }}
        </q-td>
      </template>
      <template v-slot:body-cell-acciones="props">
        <q-td :props="props">
          <q-btn flat round dense color="primary" icon="edit" @click="editarUsuario(props.row)" />
          <q-btn flat round dense color="negative" icon="delete" @click="eliminarUsuario(props.row)" />
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="dialog" persistent>
      <q-card style="min-width: 600px">
        <q-card-section>
          <div class="text-h6">{{ modoEdicion ? 'Editar Usuario' : 'Nuevo Usuario' }}</div>
        </q-card-section>

        <q-card-section>
          <q-form ref="formRef" class="q-gutter-md">
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-6">
                <q-input
                  outlined
                  v-model="form.nombres"
                  label="Nombres *"
                  :rules="[val => !!val || 'Campo requerido']"
                />
              </div>
              <div class="col-12 col-md-6">
                <q-input
                  outlined
                  v-model="form.apellidos"
                  label="Apellidos *"
                  :rules="[val => !!val || 'Campo requerido']"
                />
              </div>
              <div class="col-12">
                <q-input
                  outlined
                  v-model="form.email"
                  label="Email *"
                  type="email"
                  :rules="[val => !!val || 'Campo requerido']"
                  :readonly="modoEdicion"
                />
              </div>

              <div class="col-12 col-md-6">
                <q-select
                  outlined
                  v-model="form.perfil"
                  :options="perfilOptions"
                  label="Perfil *"
                  option-label="nombre"
                  option-value="rowid" 
                  emit-value
                  map-options
                  :rules="[val => !!val || 'Campo requerido']"
                />
              </div>

              <div class="col-12 col-md-6">
                <q-select
                  outlined
                  v-model="form.establecimiento"
                  :options="establecimientoOptions"
                  label="Establecimiento"
                  option-label="nombre"
                  option-value="rowid" 
                  emit-value
                  map-options
                  clearable
                />
              </div>

              <div class="col-12 col-md-6">
                <q-select
                  outlined
                  v-model="form.estado"
                  :options="['ACTIVO', 'INACTIVO']"
                  label="Estado *"
                />
              </div>
            </div>
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
import { usuarioService } from 'src/services/usuarioService'
import { api } from 'boot/axios'

const $q = useQuasar()
const rows = ref([])
const filter = ref('')
const loading = ref(false)
const dialog = ref(false)
const modoEdicion = ref(false)
const usuarioId = ref(null)
const formRef = ref(null)

const perfilOptions = ref([])
const establecimientoOptions = ref([])

const form = ref({
  nombres: '',
  apellidos: '',
  email: '',
  perfil: null,
  establecimiento: null,
  estado: 'ACTIVO'
})

const columns = [
  { name: 'rowid', label: 'ID', field: 'rowid', align: 'left', sortable: true },
  { name: 'nombres', label: 'Nombres', field: 'nombres', align: 'left', sortable: true },
  { name: 'apellidos', label: 'Apellidos', field: 'apellidos', align: 'left', sortable: true },
  { name: 'email', label: 'Email', field: 'email', align: 'left', sortable: true },
  { name: 'perfil', label: 'Perfil', field: 'perfil', align: 'center', sortable: true },
  { name: 'estado', label: 'Estado', field: 'estado', align: 'center', sortable: true },
  { name: 'acciones', label: 'Acciones', field: 'acciones', align: 'center' }
]

const cargarPerfiles = async () => {
  try {
    const response = await api.get('/api/seg/perfiles')
    perfilOptions.value = response.data
  } catch (error) {
    console.error('Error al cargar perfiles:', error)
    $q.notify({ type: 'negative', message: 'Error al cargar los perfiles', icon: 'error' })
  }
}

const obtenerNombrePerfil = (idPerfil) => {
  if (!perfilOptions.value.length) return idPerfil; // Si aún no cargan, muestra el número
  
  const perfil = perfilOptions.value.find(p => p.rowid === idPerfil);
  return perfil ? perfil.nombre : 'Desconocido';
}

const cargarEstablecimientos = async () => {
  try {
    const response = await api.get('/api/adm/establecimientos')
    establecimientoOptions.value = response.data
  } catch (error) {
    console.error('Error al cargar establecimientos:', error)
    $q.notify({ type: 'negative', message: 'Error al cargar los establecimientos', icon: 'error' })
  }
}

const cargarUsuarios = async () => {
  loading.value = true
  try {
    rows.value = await usuarioService.getAllUsuarios()
  } catch(error) {
    console.error("Error cargando usuarios:", error)
    $q.notify({ type: 'negative', message: 'Error al cargar usuarios' })
  } finally {
    loading.value = false
  }
}

const editarUsuario = (row) => {
  modoEdicion.value = true
  usuarioId.value = row.rowid
  // Almacenamos el row completo en el form.
  // Como ahora los selects usan option-value="rowid", 
  // Quasar sabrá buscar el número en la lista de opciones.
  form.value = { ...row }
  dialog.value = true
}

const eliminarUsuario = (row) => {
  $q.dialog({
    title: 'Confirmar',
    message: `¿Eliminar usuario ${row.nombres} ${row.apellidos}?`,
    cancel: true
  }).onOk(async () => {
    try {
      await usuarioService.deleteUsuario(row.rowid)
      $q.notify({ type: 'positive', message: 'Usuario eliminado' })
      cargarUsuarios()
    } catch {
      $q.notify({ type: 'negative', message: 'Error al eliminar' })
    }
  })
}



const guardar = async () => {
  const valid = await formRef.value.validate()
  if (!valid) return

  try {
    if (modoEdicion.value) {
      // CORRECCIÓN 3: Preparar el payload para que coincida con el backend
      // Tu backend (UsuarioController -> update) espera las llaves 'perfil' y 'establecimiento'
      const payload = {
        nombres: form.value.nombres,
        apellidos: form.value.apellidos,
        email: form.value.email,
        estado: form.value.estado,
        perfil: form.value.perfil, 
        establecimiento: form.value.establecimiento
      }
      
      await usuarioService.updateUsuario(usuarioId.value, payload)
      $q.notify({ type: 'positive', message: 'Usuario actualizado' })
    } else {
       // La creación la haces en otra vista, pero la dejamos por si acaso
    }
    dialog.value = false
    cargarUsuarios()
  } catch (error) {
    console.error(error);
    $q.notify({ type: 'negative', message: 'Error al guardar. (Revisar backend)' })
  }
}

const resetForm = () => {
  modoEdicion.value = false
  usuarioId.value = null
  form.value = {
    nombres: '',
    apellidos: '',
    email: '',
    perfil: null,
    establecimiento: null,
    estado: 'ACTIVO'
  }
  formRef.value?.resetValidation()
}

onMounted(() => {
  cargarUsuarios()
  cargarPerfiles()
  cargarEstablecimientos()
})
</script>