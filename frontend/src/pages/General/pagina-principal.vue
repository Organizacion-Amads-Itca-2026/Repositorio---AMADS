<template>
  <div class="q-pa-md">
    <!-- Header Banner -->
    <div class="bg-primary text-white q-mb-lg" style="padding: 40px 20px; border-radius: 8px;">
      <div class="row items-center">
        <div class="col">
          <div class="text-h4 text-weight-bold q-mb-md">Bienvenido a VENTRY</div>
          <p class="text-subtitle1">Sistema de Gestión de Entradas y Eventos</p>
          <p>Gestiona eventos, vende tickets y controla tus ventas de forma eficiente</p>
        </div>
        <div class="col-auto">
          <q-icon name="celebration" size="80px" style="opacity: 0.3;" />
        </div>
      </div>
    </div>

    <!-- Rol y Permisos del Usuario -->
    <q-card class="q-mb-lg bg-light-blue-1 border-left-primary">
      <q-card-section>
        <div class="row items-center">
          <q-icon name="verified_user" color="primary" size="32px" />
          <div class="q-ml-md">
            <div class="text-h6">Tu Perfil: <span class="text-primary">{{ userProfile }}</span></div>
            <p class="text-grey q-mb-none">Tienes acceso a {{ userPermissions.length }} permisos en el sistema</p>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Estadísticas Rápidas -->
    <div class="text-h5 q-mb-md text-weight-bold">Estadísticas Rápidas</div>
    <div class="row q-col-gutter-md q-mb-lg">
      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="bg-primary text-white hover-card">
          <q-card-section>
            <div class="text-h6">Eventos Activos</div>
            <div class="text-h3 text-weight-bold">{{ stats.eventosActivos }}</div>
            <q-icon
              name="event"
              size="48px"
              class="absolute-top-right q-ma-md"
              style="opacity: 0.3"
            />
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="bg-positive text-white hover-card">
          <q-card-section>
            <div class="text-h6">Tickets Vendidos</div>
            <div class="text-h3 text-weight-bold">{{ stats.ticketsVendidos }}</div>
            <q-icon
              name="confirmation_number"
              size="48px"
              class="absolute-top-right q-ma-md"
              style="opacity: 0.3"
            />
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="bg-info text-white hover-card">
          <q-card-section>
            <div class="text-h6">Total Ventas</div>
            <div class="text-h3 text-weight-bold">${{ stats.totalVentas.toFixed(2) }}</div>
            <q-icon
              name="attach_money"
              size="48px"
              class="absolute-top-right q-ma-md"
              style="opacity: 0.3"
            />
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-6 col-md-3">
        <q-card class="bg-warning text-white hover-card">
          <q-card-section>
            <div class="text-h6">Clientes</div>
            <div class="text-h3 text-weight-bold">{{ stats.totalClientes }}</div>
            <q-icon
              name="people"
              size="48px"
              class="absolute-top-right q-ma-md"
              style="opacity: 0.3"
            />
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Gráficos y Tablas -->
    <div class="row q-col-gutter-md">
      <!-- Próximos Eventos -->
      <div class="col-12 col-md-6">
        <q-card>
          <q-card-section>
            <div class="text-h6 q-mb-md">Próximos Eventos</div>
            <q-list separator>
              <q-item v-for="evento in proximosEventos" :key="evento.rowid">
                <q-item-section avatar>
                  <q-avatar color="primary" text-color="white">
                    <q-icon name="event" />
                  </q-avatar>
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ evento.nombre }}</q-item-label>
                  <q-item-label caption>{{ formatDate(evento.fechaProgramado) }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-badge :color="evento.estado === 'ACTIVO' ? 'positive' : 'grey'">
                    {{ evento.estado }}
                  </q-badge>
                </q-item-section>
              </q-item>
              <q-item v-if="proximosEventos.length === 0">
                <q-item-section>
                  <q-item-label class="text-grey">No hay eventos próximos</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>

      <!-- Ventas Recientes -->
      <div class="col-12 col-md-6">
        <q-card>
          <q-card-section>
            <div class="text-h6 q-mb-md">Ventas Recientes</div>
            <q-list separator>
              <q-item v-for="venta in ventasRecientes" :key="venta.rowid">
                <q-item-section avatar>
                  <q-avatar color="positive" text-color="white"> $ </q-avatar>
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ venta.cliente?.nombre || 'Cliente' }}</q-item-label>
                  <q-item-label caption>{{ formatDate(venta.fechaCrea) }}</q-item-label>
                </q-item-section>
                <q-item-section side>
                  <q-item-label>${{ venta.totalPagar?.toFixed(2) }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item v-if="ventasRecientes.length === 0">
                <q-item-section>
                  <q-item-label class="text-grey">No hay ventas recientes</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>

      <!-- Accesos Rápidos -->
      <div class="col-12">
        <div class="text-h5 q-mb-md text-weight-bold">Accesos Rápidos</div>
        <div class="row q-col-gutter-md">
          <div class="col-6 col-sm-4 col-md-2">
            <q-btn
              class="full-width"
              color="primary"
              icon="event"
              label="Eventos"
              stack
              size="md"
              to="/eventos"
              unelevated
              style="padding: 16px; border-radius: 8px;"
            />
          </div>
          <div class="col-6 col-sm-4 col-md-2">
            <q-btn
              class="full-width"
              color="positive"
              icon="point_of_sale"
              label="Venta"
              stack
              size="md"
              to="/punto-venta"
              unelevated
              style="padding: 16px; border-radius: 8px;"
            />
          </div>
          <div class="col-6 col-sm-4 col-md-2">
            <q-btn
              class="full-width"
              color="info"
              icon="check_circle"
              label="Validar"
              stack
              size="md"
              to="/validar-tickets"
              unelevated
              style="padding: 16px; border-radius: 8px;"
            />
          </div>
          <div class="col-6 col-sm-4 col-md-2">
            <q-btn
              class="full-width"
              color="warning"
              icon="people"
              label="Clientes"
              stack
              size="md"
              to="/clientes"
              unelevated
              style="padding: 16px; border-radius: 8px;"
            />
          </div>
          <div class="col-6 col-sm-4 col-md-2">
            <q-btn
              class="full-width"
              color="secondary"
              icon="assessment"
              label="Reportes"
              stack
              size="md"
              to="/reportes"
              unelevated
              style="padding: 16px; border-radius: 8px;"
            />
          </div>
          <div class="col-6 col-sm-4 col-md-2">
            <q-btn
              class="full-width"
              color="deep-purple"
              icon="settings"
              label="Config"
              stack
              size="md"
              to="/tipos-evento"
              unelevated
              style="padding: 16px; border-radius: 8px;"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { usePermissions } from 'src/composables/usePermissions'
import { eventoService } from 'src/services/eventoService'
//import { clienteService } from 'src/services/clienteService'
import { authService } from 'src/services/authService'

const $q = useQuasar()
const { getAllPermissions } = usePermissions()

const userPermissions = computed(() => getAllPermissions())
const userProfile = computed(() => authService.getUser()?.perfil || 'Usuario')

const stats = ref({
  eventosActivos: 0,
  ticketsVendidos: 0,
  totalVentas: 0,
  totalClientes: 0,
})

const proximosEventos = ref([])
const ventasRecientes = ref([])

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('es-SV', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const cargarEstadisticas = async () => {
  try {
    // Cargar métricas del backend (Kevin)
    const metricsData = await eventoService.getMetrics()
    stats.value = {
      eventosActivos: metricsData.totalEventos,
      ticketsVendidos: metricsData.totalTicketsVendidos,
      totalVentas: metricsData.totalVentas,
      totalClientes: metricsData.totalUsuarios,
    }

    // Cargar eventos para la lista de próximos
    const eventos = await eventoService.getAllEventos()
    proximosEventos.value = eventos
      .filter((e) => e.estado === 'ACTIVO')
      .sort((a, b) => new Date(a.fechaProgramado) - new Date(b.fechaProgramado))
      .slice(0, 5)

    ventasRecientes.value = []
  } catch (error) {
    console.error('Error cargando estadísticas:', error)
    $q.notify({
      type: 'negative',
      message: 'Error al cargar estadísticas del dashboard',
    })
  }
}

onMounted(() => {
  cargarEstadisticas()
})
</script>

<style scoped>
.hover-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
}

.hover-card:hover {
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  transform: translateY(-4px);
}

.bg-light-blue-1 {
  background-color: #e3f2fd;
}

.border-left-primary {
  border-left: 4px solid #1976d2;
}

.q-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>
