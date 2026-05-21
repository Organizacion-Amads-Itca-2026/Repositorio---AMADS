<template>
  <q-dialog v-model="show" persistent>
    <q-card style="min-width: 500px">
      <q-card-section class="row items-center q-pb-none">
        <div class="text-h6">Procesar Pago</div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>

      <q-card-section>
        <!-- Resumen de compra -->
        <div class="q-pa-md bg-grey-2 rounded-borders q-mb-md">
          <div class="row justify-between q-mb-sm">
            <span class="text-weight-medium">Subtotal:</span>
            <span>${{ subtotal.toFixed(2) }}</span>
          </div>
          <div class="row justify-between q-mb-sm" v-if="descuento > 0">
            <span class="text-weight-medium">Descuento:</span>
            <span class="text-negative">-${{ descuento.toFixed(2) }}</span>
          </div>
          <q-separator class="q-my-sm" />
          <div class="row justify-between">
            <span class="text-h6 text-weight-bold">Total a Pagar:</span>
            <span class="text-h6 text-weight-bold text-primary">${{ total.toFixed(2) }}</span>
          </div>
        </div>

        <!-- Selector de método de pago -->
        <q-select
          v-model="metodoPagoSeleccionado"
          :options="metodosPago"
          label="Método de Pago *"
          option-label="label"
          option-value="value"
          emit-value
          map-options
          :rules="[(val) => !!val || 'Debe seleccionar un método de pago']"
        >
          <template v-slot:prepend>
            <q-icon :name="getIconoPago(metodoPagoSeleccionado)" />
          </template>
        </q-select>

        <!-- Formularios específicos por método de pago -->
        <div class="q-mt-md">
          <!-- EFECTIVO -->
          <div v-if="metodoPagoSeleccionado === 'EFECTIVO'">
            <q-input
              v-model.number="formPago.montoPagado"
              label="Monto con el que paga *"
              type="number"
              step="0.01"
              prefix="$"
              :rules="[
                (val) => !!val || 'Campo requerido',
                (val) => val >= total || `Debe ser mayor o igual a $${total.toFixed(2)}`
              ]"
            >
              <template v-slot:append>
                <q-btn
                  flat
                  dense
                  label="Exacto"
                  size="sm"
                  @click="formPago.montoPagado = total"
                />
              </template>
            </q-input>

            <q-input
              v-model="cambioCalculado"
              label="Cambio"
              readonly
              prefix="$"
              class="q-mt-sm"
              :class="{ 'text-positive text-weight-bold': cambioCalculado > 0 }"
            />
          </div>

          <!-- TARJETA POS -->
          <div v-if="metodoPagoSeleccionado === 'TARJETA_POS'">
            <q-input
              v-model="formPago.numeroTarjeta"
              label="Últimos 4 dígitos de tarjeta *"
              mask="####"
              :rules="[
                (val) => !!val || 'Campo requerido',
                (val) => val.length === 4 || 'Debe ingresar 4 dígitos'
              ]"
            >
              <template v-slot:prepend>
                <q-icon name="credit_card" />
              </template>
            </q-input>

            <q-input
              v-model="formPago.autorizacionPos"
              label="Código de autorización *"
              class="q-mt-sm"
              :rules="[(val) => !!val || 'Campo requerido']"
            >
              <template v-slot:prepend>
                <q-icon name="vpn_key" />
              </template>
            </q-input>
          </div>

          <!-- TIGO MONEY -->
          <div v-if="metodoPagoSeleccionado === 'TIGO_MONEY'">
            <q-input
              v-model="formPago.numeroTelefono"
              label="Número de teléfono Tigo Money *"
              mask="####-####"
              :rules="[
                (val) => !!val || 'Campo requerido',
                (val) => val.length === 9 || 'Formato inválido'
              ]"
            >
              <template v-slot:prepend>
                <q-icon name="phone_android" />
              </template>
            </q-input>

            <q-input
              v-model="formPago.codigoTransaccion"
              label="Código de transacción *"
              class="q-mt-sm"
              hint="Código de confirmación de Tigo Money"
              :rules="[(val) => !!val || 'Campo requerido']"
            >
              <template v-slot:prepend>
                <q-icon name="tag" />
              </template>
            </q-input>
          </div>

          <!-- BITCOIN -->
          <div v-if="metodoPagoSeleccionado === 'BITCOIN'">
            <q-input
              v-model="formPago.walletAddress"
              label="Dirección de Wallet *"
              :rules="[(val) => !!val || 'Campo requerido']"
            >
              <template v-slot:prepend>
                <q-icon name="account_balance_wallet" />
              </template>
            </q-input>

            <q-input
              v-model="formPago.transactionHash"
              label="Transaction Hash *"
              class="q-mt-sm"
              hint="Hash de la transacción de Bitcoin"
              :rules="[(val) => !!val || 'Campo requerido']"
            >
              <template v-slot:prepend>
                <q-icon name="tag" />
              </template>
            </q-input>

            <div class="q-mt-sm text-caption text-grey-7">
              <q-icon name="info" size="sm" />
              Total en BTC: {{ (total / 50000).toFixed(8) }} BTC (aprox.)
            </div>
          </div>
        </div>

        <!-- Referencia adicional (opcional) -->
        <q-input
          v-model="formPago.referenciaPago"
          label="Referencia adicional (opcional)"
          class="q-mt-md"
          hint="Número de factura, recibo, etc."
        />
      </q-card-section>

      <q-card-actions align="right" class="q-pa-md">
        <q-btn
          label="Cancelar"
          flat
          color="negative"
          v-close-popup
          @click="resetForm"
        />
        <q-btn
          label="Confirmar Pago"
          color="primary"
          @click="confirmarPago"
          :loading="loading"
          :disable="!puedeConfirmar"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  modelValue: Boolean,
  subtotal: { type: Number, required: true },
  descuento: { type: Number, default: 0 },
  total: { type: Number, required: true }
})

const emit = defineEmits(['update:modelValue', 'confirmar'])

const show = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const loading = ref(false)
const metodoPagoSeleccionado = ref(null)

const metodosPago = [
  { label: 'Efectivo', value: 'EFECTIVO', icon: 'payments' },
  { label: 'Tarjeta POS', value: 'TARJETA_POS', icon: 'credit_card' },
  { label: 'Tigo Money', value: 'TIGO_MONEY', icon: 'phone_android' },
  { label: 'Bitcoin', value: 'BITCOIN', icon: 'currency_bitcoin' }
]

const formPago = ref({
  // Efectivo
  montoPagado: null,
  cambio: 0,
  // Tarjeta
  numeroTarjeta: '',
  autorizacionPos: '',
  // Tigo Money
  numeroTelefono: '',
  codigoTransaccion: '',
  // Bitcoin
  walletAddress: '',
  transactionHash: '',
  // General
  referenciaPago: ''
})

const cambioCalculado = computed(() => {
  if (!formPago.value.montoPagado) return '0.00'
  const cambio = formPago.value.montoPagado - props.total
  return cambio >= 0 ? cambio.toFixed(2) : '0.00'
})

const puedeConfirmar = computed(() => {
  if (!metodoPagoSeleccionado.value) return false

  switch (metodoPagoSeleccionado.value) {
    case 'EFECTIVO':
      return formPago.value.montoPagado >= props.total
    case 'TARJETA_POS':
      return formPago.value.numeroTarjeta.length === 4 && formPago.value.autorizacionPos
    case 'TIGO_MONEY':
      return formPago.value.numeroTelefono && formPago.value.codigoTransaccion
    case 'BITCOIN':
      return formPago.value.walletAddress && formPago.value.transactionHash
    default:
      return false
  }
})

const getIconoPago = (metodo) => {
  const found = metodosPago.find(m => m.value === metodo)
  return found ? found.icon : 'payment'
}

const confirmarPago = () => {
  if (!puedeConfirmar.value) return

  loading.value = true

  const datosPago = {
    metodoPago: metodoPagoSeleccionado.value,
    ...formPago.value,
    cambio: parseFloat(cambioCalculado.value)
  }

  // Limpiar campos no necesarios según el método de pago
  if (metodoPagoSeleccionado.value !== 'EFECTIVO') {
    delete datosPago.montoPagado
    delete datosPago.cambio
  }
  if (metodoPagoSeleccionado.value !== 'TARJETA_POS') {
    delete datosPago.numeroTarjeta
    delete datosPago.autorizacionPos
  }
  if (metodoPagoSeleccionado.value !== 'TIGO_MONEY') {
    delete datosPago.numeroTelefono
    delete datosPago.codigoTransaccion
  }
  if (metodoPagoSeleccionado.value !== 'BITCOIN') {
    delete datosPago.walletAddress
    delete datosPago.transactionHash
  }

  emit('confirmar', datosPago)
  
  setTimeout(() => {
    loading.value = false
    show.value = false
    resetForm()
  }, 500)
}

const resetForm = () => {
  metodoPagoSeleccionado.value = null
  formPago.value = {
    montoPagado: null,
    cambio: 0,
    numeroTarjeta: '',
    autorizacionPos: '',
    numeroTelefono: '',
    codigoTransaccion: '',
    walletAddress: '',
    transactionHash: '',
    referenciaPago: ''
  }
}

// Auto-seleccionar efectivo si el total es pequeño
watch(() => props.total, (newVal) => {
  if (newVal > 0 && newVal < 100 && !metodoPagoSeleccionado.value) {
    metodoPagoSeleccionado.value = 'EFECTIVO'
  }
})
</script>

<style scoped>
.rounded-borders {
  border-radius: 8px;
}
</style>