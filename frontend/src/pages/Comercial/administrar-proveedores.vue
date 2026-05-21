<template>
  <div class="q-pa-md">
    <q-btn label="Añadir Proveedor" color="secondary" @click="openDialog" />

    <q-dialog v-model="dialog" @hide="modoEdicion = false">
      <q-card style="min-width: 50vw; min-height: 60vh">
        <q-card-section class="row items-center q-pb-none text-h6"> Proveedor </q-card-section>

        <q-card-section>
          <q-form ref="form" @submit="onSubmit" @reset="onReset" class="q-gutter-md">
            <q-input
              filled
              v-model="nombreProveedor"
              label="Nombre proveedor *"
              lazy-rules="ondemand"
              :rules="[(val) => (val && val.length > 0) || 'Indica el nombre de la empresa']"
            />
            <q-input
              filled
              v-model="nombreContacto"
              label="Nombre Contacto *"
              lazy-rules="ondemand"
              :rules="[(val) => (val && val.length > 0) || 'Indica un contacto']"
            />
              <q-select
                filled
                v-model="estado"
                :options="options"
                label="Estado del evento"
                :rules="[(val) => !!val || 'Indica este campo']"
              />
            <q-select
              filled
              v-model="servicio"
              :options="optionsServicio"
              label="Tipo de servicio *"
              lazy-rules="ondemand"
              :rules="[(val) => !!val || 'Indica el tipo de servicio que suministra']"
            />
            <q-input
              filled
              mask="####-####"
              v-model="telefono"
              label="Teléfono *"
              lazy-rules="ondemand"
              :rules="[
                (val) => val && val.replace(/\D/g, '').length === 8 || 'Indica un número telefónico empresarial'
              ]"
            />
            <q-input
              filled
              v-model="direccion"
              label="Dirección *"
              lazy-rules="ondemand"
              :rules="[(val) => (val && val.length > 0) || 'Indica a dirección de la empresa']"
            />
            <q-select
              filled
              v-model="departamento"
              :options="optionsDepa"
              label="Departamento *"
              lazy-rules="ondemand"
              :rules="[(val) => !!val || 'Indica departamento']"
            />
            <q-select
              filled
              v-model="municipio"
              :options="optionsMuni"
              label="Municipio *"
              lazy-rules="ondemand"
              :rules="[(val) => !!val || 'Indica municipio']"
            />
            <q-input
              filled
              type="email"
              v-model="email"
              label="Correo de facturación *"
              lazy-rules="ondemand"
              :rules="[(val) => (val && val.length > 0) || 'Indica  electrónico de facturación']"
            />
            <q-input
              filled
              mask="#####-#"
              v-model="nrc"
              label="NRC *"
              lazy-rules="ondemand"
              :rules="[(val) => (val && val.length > 0) || 'Indica NRC de la empresa']"
            />
            <q-input
              filled
              v-model="nit"
              label="NIT *"
              mask="####-######-###-#"
              lazy-rules="ondemand"
              :rules="[
                (val) => (val && val.length > 0) || 'Indica NIT de la empresa',
                
              ]"
            />
            <div>
              <q-btn  :label="modoEdicion ? 'Editar' : 'Guardar'" type="submit" color="secondary" />
              <q-btn label="Reset" type="reset" color="secondary" flat class="q-ml-sm" />
            </div>
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Close" color="black" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-table flat bordered title="Proveedores" :rows="rows" :columns="columns" row-key="name">
      <template v-slot:header="props">
        <q-tr :props="props">
          <q-th v-for="col in props.cols" :key="col.name" :props="props">
            {{ col.label }}
          </q-th>
        </q-tr>
      </template>

      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td v-for="col in props.cols" :key="col.name" :props="props">
            <template v-if="col.name === 'accion'">
              <q-btn
                flat
                dense
                color="primary"
                icon="edit"
                label="Editar"
                @click="editarEvento(props.row)"
                class="q-mr-sm acciones"
              />
              <q-btn
                flat
                dense
                color="negative"
                icon="delete"
                label="Eliminar"
                @click="eliminarEvento(props.row)"
                class="q-mr-sm acciones"
              />
            </template>
            <template v-else>
              {{ col.value }}
            </template>
          </q-td>
        </q-tr>
      </template>
    </q-table>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useQuasar } from 'quasar'
//import axios from 'axios'


onMounted(() => {
  mostrarProveedores()
})
import { proveedorService } from 'src/services/proveedorService'
const $q = useQuasar()
const form = ref(null)
const nombreProveedor = ref('')
const nombreContacto = ref('')
const email = ref('')
const telefono = ref('')
const nit = ref('')
const nrc = ref('')
const direccion = ref('')
const estado = ref(null)
const options = ['Activo', 'Cancelado', 'Reprogramado', 'Vendido']
const servicio = ref(null)
const departamento = ref(null)
const municipio = ref(null)
const dialog = ref(false)
const modoEdicion = ref(false)
const proveedorId = ref(null)
const optionsServicio = ['Sonido', 'Luces', 'Escenografía', 'Audio y Visual', 'Catering']
const optionsDepa = [
  'Ahuachapán',
  'Santa Ana',
  'Sonsonate',
  'Chalatenango',
  'La Libertad',
  'San Salvador',
  'Cuscatlán',
  'La Paz',
  'Cabañas',
  'San Vicente',
  'Usulután',
  'San Miguel',
  'Morazán',
  'La Unión',
]
const optionsMuni = ref([])
const municipios = {
  Ahuachapán: [
    'Ahuachapán',
    'Apaneca',
    'Atiquizaya',
    'Concepción de Ataco',
    'El Refugio',
    'Guaymango',
    'Jujutla',
    'San Francisco Menéndez',
    'San Lorenzo',
    'San Pedro Puxtla',
    'Tacuba',
    'Turín',
  ],
  'Santa Ana': [
    'Candelaria de la Frontera',
    'Chalchuapa',
    'Coatepeque',
    'El Congo',
    'El Porvenir',
    'Masahuat',
    'Metapán',
    'San Antonio Pajonal',
    'San Sebastián Salitrillo',
    'Santa Ana',
    'Santa Rosa Guachipilín',
    'Santiago de la Frontera',
    'Texistepeque',
  ],
  Sonsonate: [
    'Acajutla',
    'Armenia',
    'Caluco',
    'Cuisnahuat',
    'Izalco',
    'Juayúa',
    'Nahuizalco',
    'Nahulingo',
    'Salcoatitán',
    'San Antonio del Monte',
    'San Julián',
    'Santa Catarina Masahuat',
    'Santa Isabel Ishuatán',
    'Santo Domingo Guzmán',
    'Sonsonate',
    'Sonzacate',
  ],
  Chalatenango: [
    'Agua Caliente',
    'Arcatao',
    'Azacualpa',
    'Chalatenango',
    'Comalapa',
    'Concepción Quezaltepeque',
    'Citalá',
    'Dulce Nombre de María',
    'El Carrizal',
    'El Paraíso',
    'La Laguna',
    'La Palma',
    'La Reina',
    'Las Vueltas',
    'Nombre de Jesús',
    'Nueva Concepción',
    'Nueva Trinidad',
    'Ojos de Agua',
    'Potonico',
    'San Antonio de la Cruz',
    'San Antonio Los Ranchos',
    'San Fernando',
    'San Francisco Lempa',
    'San Francisco Morazán',
    'San Ignacio',
    'San Isidro Labrador',
    'San José Cancasque',
    'San José Las Flores',
    'San Luis del Carmen',
    'San Miguel de Mercedes',
    'San Rafael',
    'Santa Rita',
    'Tejutla',
  ],
  'La Libertad': [
    'Antiguo Cuscatlán',
    'Chiltiupán',
    'Ciudad Arce',
    'Colón',
    'Comasagua',
    'Huizúcar',
    'Jayaque',
    'Jicalapa',
    'La Libertad',
    'Nuevo Cuscatlán',
    'Quezaltepeque',
    'San José Villanueva',
    'San Juan Opico',
    'San Matías',
    'San Pablo Tacachico',
    'Santa Tecla',
    'Talnique',
    'Tamanique',
    'Teotepeque',
    'Tepecoyo',
    'Zaragoza',
  ],
  'San Salvador': [
    'Aguilares',
    'Apopa',
    'Ayutuxtepeque',
    'Cuscatancingo',
    'Delgado',
    'El Paisnal',
    'Guazapa',
    'Ilopango',
    'Mejicanos',
    'Nejapa',
    'Panchimalco',
    'Rosario de Mora',
    'San Marcos',
    'San Martín',
    'San Salvador',
    'Santiago Texacuangos',
    'Santo Tomás',
    'Soyapango',
    'Tonacatepeque',
  ],
  Cuscatlán: [
    'Candelaria',
    'Cojutepeque',
    'El Carmen',
    'El Rosario',
    'Monte San Juan',
    'Oratorio de Concepción',
    'San Bartolomé Perulapía',
    'San Cristóbal',
    'San José Guayabal',
    'San Pedro Perulapán',
    'San Rafael Cedros',
    'San Ramón',
    'Santa Cruz Analquito',
    'Santa Cruz Michapa',
    'Suchitoto',
    'Tenancingo',
  ],
  'La Paz': [
    'Cuyultitán',
    'El Rosario',
    'Jerusalén',
    'Mercedes La Ceiba',
    'Olocuilta',
    'Paraíso de Osorio',
    'San Antonio Masahuat',
    'San Emigdio',
    'San Francisco Chinameca',
    'San Juan Nonualco',
    'San Juan Talpa',
    'San Juan Tepezontes',
    'San Luis La Herradura',
    'San Luis Talpa',
    'San Miguel Tepezontes',
    'San Pedro Masahuat',
    'San Pedro Nonualco',
    'San Rafael Obrajuelo',
    'Santa María Ostuma',
    'Santiago Nonualco',
    'Tapalhuaca',
    'Zacatecoluca',
  ],
  Cabañas: [
    'Cinquera',
    'Dolores',
    'Guacotecti',
    'Ilobasco',
    'Jutiapa',
    'San Isidro',
    'Sensuntepeque',
    'Tejutepeque',
    'Victoria',
  ],
  'San Vicente': [
    'Apastepeque',
    'Guadalupe',
    'San Cayetano Istepeque',
    'San Esteban Catarina',
    'San Ildefonso',
    'San Lorenzo',
    'San Sebastián',
    'San Vicente',
    'Santa Clara',
    'Santo Domingo',
    'Tecoluca',
    'Tepetitán',
    'Verapaz',
  ],
  Usulután: [
    'Alegría',
    'Berlín',
    'California',
    'Concepción Batres',
    'El Triunfo',
    'Ereguayquín',
    'Estanzuelas',
    'Jiquilisco',
    'Jucuapa',
    'Jucuarán',
    'Mercedes Umaña',
    'Nueva Granada',
    'Ozatlán',
    'Puerto El Triunfo',
    'San Agustín',
    'San Buenaventura',
    'San Dionisio',
    'San Francisco Javier',
    'Santa Elena',
    'Santa María',
    'Santiago de María',
    'Tecapán',
    'Usulután',
  ],
  'San Miguel': [
    'Carolina',
    'Chapeltique',
    'Chinameca',
    'Chirilagua',
    'Ciudad Barrios',
    'Comacarán',
    'El Tránsito',
    'Lolotique',
    'Moncagua',
    'Nueva Guadalupe',
    'Quelepa',
    'San Antonio del Mosco',
    'San Gerardo',
    'San Jorge',
    'San Luis de la Reina',
    'San Miguel',
    'San Rafael Oriente',
    'Sesori',
    'Uluazapa',
  ],
  Morazán: [
    'Arambala',
    'Cacaopera',
    'Chilanga',
    'Corinto',
    'Delicias de Concepción',
    'El Divisadero',
    'El Rosario',
    'Gualococti',
    'Guatajiagua',
    'Joateca',
    'Jocoaitique',
    'Jocoro',
    'Lolotiquillo',
    'Meanguera',
    'Osicala',
    'Perquín',
    'San Carlos',
    'San Fernando',
    'San Francisco Gotera',
    'San Isidro',
    'San Simón',
    'Sensembra',
    'Sociedad',
    'Torola',
    'Yamabal',
    'Yoloaiquín',
  ],
  'La Unión': [
    'Anamorós',
    'Bolívar',
    'Concepción de Oriente',
    'Conchagua',
    'El Carmen',
    'El Sauce',
    'Intipucá',
    'La Unión',
    'Lislique',
    'Meanguera del Golfo',
    'Nueva Esparta',
    'Pasaquina',
    'Polorós',
    'San Alejo',
    'San José',
    'Santa Rosa de Lima',
    'Yayantique',
    'Yucuaiquín',
  ],
}

watch(departamento, (nuevoDepartamento) => {
  if (nuevoDepartamento && municipios[nuevoDepartamento]) {
    optionsMuni.value = municipios[nuevoDepartamento]
  } else {
    optionsMuni.value = []
  }

  if (!modoEdicion.value) {
    municipio.value = null
  }
})


const columns = [
  {
    name: 'nombreEmpresa',
    required: true,
    label: 'Proveedor',
    align: 'left',
    field: 'nombreEmpresa',
    format: (val) => `${val}`,
    sortable: true,
  },
  {
    name: 'nombreContacto',
    label: 'Nombre contacto',
    field: 'nombreContacto',
    align: 'center',
    sortable: true,
  },
  {
    name: 'email',
    label: 'Email',
    field: 'email',
    align: 'center',
    sortable: true,
  },
  {
    name: 'telefono',
    label: 'Telefono',
    field: 'telefono',
    align: 'center',
    sortable: true,
  },
  {
    name: 'nit',
    label: 'NIT',
    field: 'nit',
    align: 'center',
    sortable: true,
  },
  {
    name: 'nrc',
    label: 'NRC',
    field: 'nrc',
    align: 'center',
    sortable: true,
  },
  {
    name: 'servicio',
    label: 'Servicio',
    field: 'servicio',
    align: 'center',
    sortable: true,
  },
  {
    name: 'direccion',
    label: 'Direccion',
    field: 'direccion',
    align: 'center',
    sortable: true,
  },
  {
    name: 'accion',
    label: 'Acciones',
    align: 'center',
  },
]

const rows = ref([])
const mostrarProveedores = async () => {
  try {
    $q.loading.show({ message: 'Proveedores...' })
    const response = await proveedorService.getAllProveedores()
    rows.value = response.map((event) => ({
      name: event.rowid,
      nombreEmpresa: event.nombreEmpresa,
      nombreContacto: event.nombreContacto,
      email: event.email,
      telefono: event.telefono,
      nit: event.nit,
      nrc: event.nrc,
      servicio: event.tipoServicio,
      direccion: event.direccion,
      depa: event.departamentoGeografico,
      muni: event.municipioGeografico,
      estado: event.estado
    }))
    $q.loading.hide()
  } catch (err) {
    $q.loading.hide()
    console.error('Error al obtener los proveedores:', err.response?.data)
    $q.notify({
      type: 'negative',
      message: err.response?.data?.mensaje || 'Error al cargar los proveedores',
      position: 'bottom',
      timeout: 3000,
      icon: 'error',
    })
  }
}

const openDialog = () => {
  if (!modoEdicion.value) {
    onReset()
  }
  dialog.value = true
}



const onSubmit = async () => {
  const proveedorData = {
    // rowid se genera automáticamente en el backend
    nombreEmpresa: nombreProveedor.value,
    nombreContacto: nombreContacto.value,
    tipoServicio: servicio.value,
    telefono: telefono.value.replace(/\D/g, ''), // limpia máscara
    email: email.value,
    nit: nit.value.replace(/\D/g, ''),
    nrc: nrc.value.replace(/\D/g, ''),
    direccion: direccion.value,
    departamentoGeografico: departamento.value,
    municipioGeografico: municipio.value,
    estado: (estado.value).toUpperCase(),
    usuarioUpdt: "Usuario",
  }

  try {
    if (modoEdicion.value) {
      // Edición
      await proveedorService.updateProveedor(proveedorId.value,proveedorData);
      $q.notify({
        message: 'Proveedor actualizado correctamente',
        color: 'positive',
        position: 'top',
      })
    } else {
      // Creación
      await proveedorService.createProveedor(proveedorData);
      $q.notify({
        message: `Proveedor ${nombreProveedor.value} agregado`,
        color: 'positive',
        position: 'top',
      })
    }

    dialog.value = false
    onReset()
    mostrarProveedores()
  } catch (error) {
    console.error('Error en submit:', error.response?.data)
    $q.notify({
      message: error.response?.data?.mensaje || 'Error al guardar el proveedor',
      color: 'negative',
      position: 'top',
    })
  }
}
const onReset = () => {
  nombreProveedor.value = ''
  nombreContacto.value = ''
  direccion.value = ''
  email.value = ''
  telefono.value = ''
  nrc.value = ''
  nit.value = ''
  estado.value=null
  servicio.value = null
  departamento.value = null
  municipio.value = null
  if (form.value) {
    form.value.resetValidation()
  }
}

const editarEvento = (row) => {
  modoEdicion.value = true
  proveedorId.value = row.name

 /* $q.notify({
    message: `Editar Proveedor: ${row.name}`,
    color: 'primary',
    position: 'top',
  })*/
  nombreProveedor.value = row.nombreEmpresa
  nombreContacto.value = row.nombreContacto
  servicio.value = row.servicio
  email.value = row.email
  telefono.value = row.telefono ? row.telefono.replace(/\D/g, '') : ''
  nit.value = row.nit ? row.nit.replace(/\D/g, '') : ''
  nrc.value = row.nrc ? row.nrc.replace(/\D/g, '') : ''
  direccion.value = row.direccion
  departamento.value = row.depa
  estado.value= row.estado
  if (municipios[row.depa]) {
    optionsMuni.value = municipios[row.depa]
  }
  municipio.value = row.muni
  openDialog()
}

const eliminarEvento = (row) => {
  $q.dialog({
    title: 'Confirmar Eliminación',
    message: `¿Eliminar el proveedor ${row.nombreEmpresa}?`,
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    try {
    await proveedorService.deleteProveedor(row.name);
      $q.notify({
        message: `Proveedor ${row.nombreEmpresa} eliminado`,
        color: 'negative',
        position: 'top',
      })
      mostrarProveedores()
    } catch (error) {
      $q.notify({
        message: error.response?.data?.message || 'Error al eliminar el evento',
        color: 'warning',
        position: 'top',
      })
    }
  })
}
</script>

<style scoped>
.q-table th {
  font-weight: bold;
}
.q-table {
  border-radius: 10px;
}
.acciones {
  margin: 0 4px;
  width: 50px;
  height: 30px;
  font-size: 12px;
}
</style>