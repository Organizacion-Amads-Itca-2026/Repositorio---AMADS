<template>
  <div class="q-pa-md">
    <q-table
      title="Lugares de Evento"
      :rows="rows"
      :columns="columns"
      row-key="rowid"
    >
      <template v-slot:top-right>
        <q-btn color="primary" label="Nuevo Lugar" @click="openDialog()" />
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
          <div class="text-h6">{{ modoEdicion ? 'Editar' : 'Nuevo' }} Lugar</div>
        </q-card-section>
        <q-card-section>
          <q-input v-model="form.nombre" label="Nombre del Lugar *" />
          <q-input v-model="form.direccion" class="q-mt-md" label="Dirección" />
          <q-select
              filled
              class="q-mt-md"
              v-model="form.departamentoGeografico"
              :options="optionsDepa"
              label="Departamento *"
              lazy-rules="ondemand"
              :rules="[(val) => !!val || 'Indica departamento']"
            />
            <q-select
              filled
              v-model="form.municipioGeografico"
              :options="optionsMuni"
              label="Municipio *"
              lazy-rules="ondemand"
              :rules="[(val) => !!val || 'Indica municipio']"
            />
          <q-input v-model.number="form.capacidadMaxima" label="Capacidad Máxima" type="number" />
          <q-select v-model="form.estado" :options="['ACTIVO', 'INACTIVO']" label="Estado *" class="q-mt-md"/>
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
import { ref, onMounted, watch } from 'vue'
import { useQuasar } from 'quasar'
import { eventoService } from 'src/services/eventoService'

const $q = useQuasar()
const rows = ref([])
const dialog = ref(false)
const modoEdicion = ref(false)
const lugarId = ref(null)

const form = ref({
  nombre: '',
  direccion: '',
  capacidadMaxima: 0,
  estado: 'ACTIVO',
  latitud: 0,
  longitud: 0,
  departamentoGeografico: null,
  municipioGeografico: null
})
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

watch(
  () => form.value.departamentoGeografico,
  (nuevoDepartamento) => {
    if (nuevoDepartamento && municipios[nuevoDepartamento]) {
      optionsMuni.value = municipios[nuevoDepartamento]
    } else {
      optionsMuni.value = []
    }

    if (!modoEdicion.value) {
      form.value.municipioGeografico = null
    }
  }
)



const columns = [
  { name: 'nombre', label: 'Nombre', field: 'nombre', align: 'left', sortable: true },
  { name: 'direccion', label: 'Dirección', field: 'direccion', align: 'left' },
  { name: 'capacidadMaxima', label: 'Capacidad', field: 'capacidadMaxima', align: 'center' },
  { name: 'estado', label: 'Estado', field: 'estado', align: 'center' },
  { name: 'acciones', label: 'Acciones', align: 'center' }
]

const cargarLugares = async () => {
  try {
    rows.value = await eventoService.getAllLugares()
    //console.log(rows.value)
  } catch {
    $q.notify({ type: 'negative', message: 'Error al cargar lugares' })
  }
}

const openDialog = () => {
  resetForm()
  dialog.value = true
}

const editar = (row) => {
  modoEdicion.value = true
  lugarId.value = row.rowid
  form.value = { ...row }
  dialog.value = true
}

const eliminar = (row) => {
  $q.dialog({
    title: 'Confirmar',
    message: `¿Eliminar lugar ${row.nombre}?`,
    cancel: true
  }).onOk(async () => {
    try {
      await eventoService.deleteLugar(row.rowid)
      $q.notify({ type: 'positive', message: 'Lugar eliminado' })
      cargarLugares()
    } catch {
      $q.notify({ type: 'negative', message: 'Error al eliminar' })
    }
  })
}

const guardar = async () => {
  try {
    if (modoEdicion.value) {
      await eventoService.updateLugar(lugarId.value, form.value)
      $q.notify({ type: 'positive', message: 'Lugar actualizado' })
    } else {
      await eventoService.createLugar(form.value)
      $q.notify({ type: 'positive', message: 'Lugar creado' })
    }
    dialog.value = false
    cargarLugares()
  } catch {
    $q.notify({ type: 'negative', message: 'Error al guardar' })
  }
}

const resetForm = () => {
  modoEdicion.value = false
  lugarId.value = null
  form.value = {
    nombre: '',
    direccion: '',
    capacidadMaxima: 0,
    estado: 'ACTIVO',
    latitud: 0,
    longitud: 0,
    departamentoGeografico: null,
    municipioGeografico: null
  }
}

onMounted(() => {
  cargarLugares()
})
</script>
