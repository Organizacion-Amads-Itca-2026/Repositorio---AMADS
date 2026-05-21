<template>
  <div class="q-pa-md">
    <q-table
      title="Descuentos y Cupones"
      :rows="rows"
      :columns="columns"
      row-key="rowid"
      :filter="filter"
    >
      <template v-slot:top-right>
        <q-input dense debounce="300" v-model="filter" placeholder="Buscar">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
        <q-btn color="primary" label="Nuevo Descuento" @click="openDialog()" class="q-ml-md" />
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
          <q-btn flat round dense color="primary" icon="edit" @click="editar(props.row)" />
          <q-btn flat round dense color="negative" icon="delete" @click="eliminar(props.row)" />
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="dialog" persistent>
      <q-card style="min-width: 600px">
        <q-card-section>
          <div class="text-h6">{{ modoEdicion ? 'Editar' : 'Nuevo' }} Descuento</div>
        </q-card-section>

        <q-card-section>
          <q-input v-model="form.codigo" label="Código *" hint="Ej: VERANO2025" />

          <q-input
            v-model="form.descripcion"
            label="Descripción"
            type="textarea"
            rows="2"
          />

          <q-select
            v-model="form.tipoDescuento"
            :options="['PORCENTAJE', 'MONTO_FIJO']"
            label="Tipo de Descuento *"
          />

          <q-input
            v-model.number="form.valorDescuento"
            label="Valor del Descuento *"
            type="number"
            :suffix="form.tipoDescuento === 'PORCENTAJE' ? '%' : '$'"
          />

          <div class="row q-gutter-sm">
            <q-input
              v-model="form.fechaInicio"
              label="Fecha Inicio *"
              type="date"
              class="col"
            />
            <q-input
              v-model="form.fechaFin"
              label="Fecha Fin *"
              type="date"
              class="col"
            />
          </div>

          <q-input
            v-model.number="form.cantidadMaximaUso"
            label="Cantidad Máxima de Uso"
            type="number"
            hint="Dejar vacío para ilimitado"
          />

          <q-select
            v-model="form.estado"
            :options="['ACTIVO', 'INACTIVO']"
            label="Estado *"
          />
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
import { descuentoService } from 'src/services/descuentoService'

const $q = useQuasar()
const rows = ref([])
const filter = ref('')
const dialog = ref(false)
const modoEdicion = ref(false)
const descuentoId = ref(null)

const form = ref({
  codigo: '',
  descripcion: '',
  tipoDescuento: 'PORCENTAJE',
  valorDescuento: 0,
  fechaInicio: '',
  fechaFin: '',
  cantidadMaximaUso: null,
  cantidadUsada: 0,
  estado: 'ACTIVO'
})

const columns = [
  { name: 'codigo', label: 'Código', field: 'codigo', align: 'left', sortable: true },
  { name: 'descripcion', label: 'Descripción', field: 'descripcion', align: 'left' },
  { name: 'tipoDescuento', label: 'Tipo', field: 'tipoDescuento', align: 'center' },
  { name: 'valorDescuento', label: 'Valor', field: 'valorDescuento', align: 'right' },
  { name: 'fechaInicio', label: 'Inicio', field: 'fechaInicio', align: 'center' },
  { name: 'fechaFin', label: 'Fin', field: 'fechaFin', align: 'center' },
  { name: 'cantidadUsada', label: 'Usados', field: 'cantidadUsada', align: 'center' },
  { name: 'estado', label: 'Estado', align: 'center' },
  { name: 'acciones', label: 'Acciones', align: 'center' }
]

const cargarDescuentos = async () => {
  try {
    rows.value = await descuentoService.getAllDescuentos()
  } catch {
    $q.notify({ type: 'negative', message: 'Error al cargar descuentos' })
  }
}

const openDialog = () => {
  resetForm()
  dialog.value = true
}

const editar = (row) => {
  modoEdicion.value = true
  descuentoId.value = row.rowid
  form.value = { ...row }
  dialog.value = true
}

const eliminar = (row) => {
  $q.dialog({
    title: 'Confirmar',
    message: `¿Eliminar descuento ${row.codigo}?`,
    cancel: true
  }).onOk(async () => {
    try {
      await descuentoService.deleteDescuento(row.rowid)
      $q.notify({ type: 'positive', message: 'Descuento eliminado' })
      cargarDescuentos()
    } catch {
      $q.notify({ type: 'negative', message: 'Error al eliminar' })
    }
  })
}

const guardar = async () => {
  try {
    if (modoEdicion.value) {
      await descuentoService.updateDescuento(descuentoId.value, form.value)
      $q.notify({ type: 'positive', message: 'Descuento actualizado' })
    } else {
      await descuentoService.createDescuento(form.value)
      $q.notify({ type: 'positive', message: 'Descuento creado' })
    }
    dialog.value = false
    cargarDescuentos()
  } catch {
    $q.notify({ type: 'negative', message: 'Error al guardar' })
  }
}

const resetForm = () => {
  modoEdicion.value = false
  descuentoId.value = null
  form.value = {
    codigo: '',
    descripcion: '',
    tipoDescuento: 'PORCENTAJE',
    valorDescuento: 0,
    fechaInicio: '',
    fechaFin: '',
    cantidadMaximaUso: null,
    cantidadUsada: 0,
    estado: 'ACTIVO'
  }
}

onMounted(() => {
  cargarDescuentos()
})
</script>
