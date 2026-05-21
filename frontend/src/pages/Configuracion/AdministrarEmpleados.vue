<template>
  <q-page class="q-pa-md">
    <q-card flat bordered>
      <q-card-section>
        <div class="text-h6">Administrar Empleados</div>
      </q-card-section>

      <q-card-section>
        <q-table
          :rows="rows"
          :columns="columns"
          row-key="rowid"
          :filter="filter"
          :loading="loading"
        >
          <template v-slot:top>
            <q-btn
              color="primary"
              icon="add"
              label="Crear Empleado"
              @click="abrirDialogoCrear"
            />
            <q-space />
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

          <template v-slot:body-cell-acciones="props">
            <q-td :props="props">
              <q-btn flat round dense color="primary" icon="edit" @click="abrirDialogoEditar(props.row)" />
              <q-btn flat round dense color="negative" icon="delete" @click="eliminar(props.row)" />
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <q-dialog v-model="dialog" persistent>
      <q-card style="min-width: 80vw; max-width: 900px">
        <q-card-section class="bg-primary text-white">
          <div class="text-h6">{{ modoEdicion ? 'Editar Empleado' : 'Nuevo Empleado' }}</div>
        </q-card-section>

        <q-card-section>
          <q-form ref="formRef" class="q-gutter-md">
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-6">
                <q-input outlined v-model="form.nombres" label="Nombres *" :rules="[val => !!val || 'Requerido']" />
              </div>
              <div class="col-12 col-md-6">
                <q-input outlined v-model="form.apellidos" label="Apellidos *" :rules="[val => !!val || 'Requerido']" />
              </div>
              <div class="col-12 col-md-4">
                <q-input outlined v-model="form.dui" label="DUI *" :rules="[val => !!val || 'Requerido']" />
              </div>
              <div class="col-12 col-md-4">
                <q-input outlined v-model="form.telefono" label="Teléfono *" :rules="[val => !!val || 'Requerido']" />
              </div>
              <div class="col-12 col-md-4">
                <q-input outlined v-model="form.email" label="Email" type="email" />
              </div>
              <div class="col-12 col-md-4">
                <q-input outlined v-model="form.genero" label="Género" />
              </div>
              <div class="col-12 col-md-4">
                <q-input outlined v-model="form.estadoCivil" label="Estado Civil" />
              </div>
              <div class="col-12 col-md-4">
                <q-input outlined v-model="form.fechaNacimiento" label="Fecha Nacimiento" type="date" stack-label />
              </div>

              <q-separator class="col-12 q-my-md" />
              <div class="col-12">
                <q-input outlined v-model="form.direccion" label="Dirección Completa *" :rules="[val => !!val || 'Requerido']" />
              </div>
              <div class="col-12 col-md-6">
                <q-input outlined v-model="form.departamentoGeografico" label="Departamento *" :rules="[val => !!val || 'Requerido']" />
              </div>
              <div class="col-12 col-md-6">
                <q-input outlined v-model="form.municipioGeografico" label="Municipio *" :rules="[val => !!val || 'Requerido']" />
              </div>

              <q-separator class="col-12 q-my-md" />
              <div class="col-12 col-md-4">
                <q-select
                  outlined
                  v-model="form.areaId"
                  :options="areaOptions"
                  label="Área de Trabajo *"
                  option-label="nombre"
                  option-value="rowid"
                  emit-value map-options
                  :rules="[val => !!val || 'Requerido']"
                />
              </div>
              <div class="col-12 col-md-4">
                <q-select
                  outlined
                  v-model="form.cargoId"
                  :options="cargoOptions"
                  label="Cargo *"
                  option-label="nombre"
                  option-value="rowid"
                  emit-value map-options
                  :rules="[val => !!val || 'Requerido']"
                />
              </div>
              <div class="col-12 col-md-4">
                <q-select
                  outlined
                  v-model="form.supervisorId"
                  :options="supervisorOptions"
                  label="Supervisor"
                  option-label="nombres"
                  option-value="rowid"
                  emit-value map-options clearable
                />
              </div>
              <div class="col-12 col-md-4">
                <q-input outlined v-model="form.fechaContratacion" label="Fecha Contratación *" type="date" stack-label :rules="[val => !!val || 'Requerido']" />
              </div>
              <div class="col-12 col-md-4">
                <q-input outlined v-model="form.tipoContrato" label="Tipo de Contrato" />
              </div>
              <div class="col-12 col-md-4">
                <q-input outlined v-model.number="form.salario" label="Salario" type="number" prefix="$" />
              </div>
              <div class="col-12 col-md-4">
                <q-select
                  outlined
                  v-model="form.estado"
                  :options="['ACTIVO', 'INACTIVO']"
                  label="Estado *"
                  :rules="[val => !!val || 'Requerido']"
                />
              </div>

            </div>
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" color="grey" v-close-popup @click="resetForm" />
          <q-btn label="Guardar" color="primary" @click="guardar" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { api } from 'boot/axios'
import { empleadoService } from 'src/services/empleadoService'

const $q = useQuasar()
const rows = ref([])
const filter = ref('')
const loading = ref(false)
const dialog = ref(false)
const modoEdicion = ref(false)
const empleadoId = ref(null)
const formRef = ref(null)

const columns = [
  { name: 'nombres', label: 'Nombres', field: 'nombres', align: 'left', sortable: true },
  { name: 'apellidos', label: 'Apellidos', field: 'apellidos', align: 'left', sortable: true },
  { name: 'dui', label: 'DUI', field: 'dui', align: 'left', sortable: true },
  { name: 'nombreCargo', label: 'Cargo', field: 'nombreCargo', align: 'left', sortable: true },
  { name: 'nombreArea', label: 'Área', field: 'nombreArea', align: 'left', sortable: true },
  { name: 'estado', label: 'Estado', field: 'estado', align: 'center', sortable: true },
  { name: 'acciones', label: 'Acciones', align: 'center' }
]

const formVacio = {
  nombres: '',
  apellidos: '',
  dui: '',
  telefono: '',
  email: '',
  departamentoGeografico: '',
  municipioGeografico: '',
  direccion: '',
  genero: '',
  estadoCivil: '',
  fechaNacimiento: null,
  areaId: null,
  cargoId: null,
  supervisorId: null,
  fechaContratacion: null,
  tipoContrato: '',
  salario: 0.0,
  estado: 'ACTIVO'
}
const form = ref({ ...formVacio })

const areaOptions = ref([])
const cargoOptions = ref([])
const supervisorOptions = ref([])

const cargarEmpleados = async () => {
  loading.value = true
  try {
    const data = await empleadoService.getAllEmpleados()
    rows.value = data
    supervisorOptions.value = data.map(e => ({ rowid: e.rowid, nombres: `${e.nombres} ${e.apellidos}` }))
  } catch (error) {
    console.error('Error al cargar empleados:', error) // <-- ARREGLADO
    $q.notify({ type: 'negative', message: 'Error al cargar empleados' })
  } finally {
    loading.value = false
  }
}

const cargarAreas = async () => {
  try {
    areaOptions.value = (await api.get('/api/adm/areas-trabajo')).data
  } catch (error) {
    console.error('Error al cargar áreas:', error) // <-- ARREGLADO
    $q.notify({ type: 'negative', message: 'Error al cargar áreas' })
  }
}

const cargarCargos = async () => {
  try {
    cargoOptions.value = (await api.get('/api/adm/cargos')).data
  } catch (error) {
    console.error('Error al cargar cargos:', error) // <-- ARREGLADO
    $q.notify({ type: 'negative', message: 'Error al cargar cargos' })
  }
}

const resetForm = () => {
  form.value = { ...formVacio }
  formRef.value?.resetValidation()
  modoEdicion.value = false
  empleadoId.value = null
}

const abrirDialogoCrear = () => {
  resetForm()
  dialog.value = true
}

const abrirDialogoEditar = async (row) => { // 1. Convertir en async
  resetForm()
  modoEdicion.value = true
  empleadoId.value = row.rowid

  try {
    $q.loading.show({ message: 'Cargando datos del empleado...' })
    
    // 2. Llamar al nuevo endpoint que devuelve el DTO con IDs
    const empleadoDTO = await empleadoService.getEmpleadoDTOById(row.rowid)
    
    // 3. Llenar el formulario con los datos COMPLETOS
    form.value = empleadoDTO
    
    $q.loading.hide()
    dialog.value = true // Abrir el diálogo SOLO si los datos cargaron
    
  } catch (error) {
    console.error('Error al cargar datos para editar:', error)
    $q.loading.hide()
    $q.notify({ type: 'negative', message: 'Error al cargar los datos del empleado.' })
  }
}

const guardar = async () => {
  const valid = await formRef.value.validate()
  if (!valid) return

  try {
    $q.loading.show({ message: 'Guardando...' })
    if (modoEdicion.value) {
      await empleadoService.updateEmpleado(empleadoId.value, form.value)
      $q.notify({ type: 'positive', message: 'Empleado actualizado' })
    } else {
      await empleadoService.createEmpleado(form.value)
      $q.notify({ type: 'positive', message: 'Empleado creado' })
    }
    dialog.value = false
    await cargarEmpleados() 
  } catch (error) {
    console.error('Error al guardar:', error) // <-- ARREGLADO
    $q.notify({ type: 'negative', message: 'Error al guardar el empleado' })
  } finally {
    $q.loading.hide()
  }
}

const eliminar = (row) => {
  $q.dialog({
    title: 'Confirmar',
    message: `¿Está seguro de eliminar a ${row.nombres} ${row.apellidos}?`,
    cancel: true,
    persistent: true
  }).onOk(async () => {
    try {
      $q.loading.show({ message: 'Eliminando...' })
      await empleadoService.deleteEmpleado(row.rowid)
      $q.notify({ type: 'positive', message: 'Empleado eliminado' })
      await cargarEmpleados() 
    } catch (error) {
      console.error('Error al eliminar:', error) // <-- ARREGLADO
      $q.notify({ type: 'negative', message: 'Error al eliminar' })
    } finally {
      $q.loading.hide()
    }
  })
}

onMounted(() => {
  cargarEmpleados()
  cargarAreas()
  cargarCargos()
})
</script>