<template>
  <q-list>
    <q-expansion-item
      v-for="(group, category) in groupedLinks"
      :key="category"
      group="agrupacion"
      :label="category"
      :icon="group[0]?.ICONO_PADRE"
      expand-separator
    >
      <q-list>
        <q-item v-for="link in group" :key="link.ROWID" clickable tag="router-link" :to="link.URL">
          <q-item-section avatar>
            <q-icon v-if="link.ICONO" :name="link.ICONO" class="text-primary" />
            <q-icon v-else name="mdi-circle" class="text-grey" />
          </q-item-section>
          <q-item-section>
            <q-item-label>{{ link.NOMBRE_RECURSO }}</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-expansion-item>
  </q-list>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  links: {
    type: Array,
    required: true,
  },
})

// Agrupar por nombreMenuPadre (Clientes, Facturación, etc.)
const groupedLinks = computed(() => {
  const filteredLinks = props.links.filter((link) => link.NOMBRE_PERMISO === 'VER')
  return filteredLinks.reduce((groups, link) => {
    const category = link.NOMBRE_PADRE || 'Otros'
    if (!groups[category]) groups[category] = []
    groups[category].push(link)
    return groups
  }, {})
})
</script>
