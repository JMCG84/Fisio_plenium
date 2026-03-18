<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import type { Pedido } from '../types';

const pedidos = ref<Pedido[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);
const statusMessage = ref<{ type: 'success' | 'danger', text: string } | null>(null);
const expandedPedidoId = ref<number | null>(null);

const toggleRow = (id: number) => {
  expandedPedidoId.value = expandedPedidoId.value === id ? null : id;
};

const showStatus = (text: string, type: 'success' | 'danger' = 'success') => {
  statusMessage.value = { text, type };
  setTimeout(() => {
    statusMessage.value = null;
  }, 4000);
};

const fetchReservations = async () => {
  loading.value = true;
  try {
    const response = await axios.get('http://localhost/fisio_Plenium/api/admin_get_reservations.php');
    if (response.data.success) {
      pedidos.value = response.data.data;
    } else {
      error.value = 'No se pudieron cargar las reservas.';
    }
  } catch (err) {
    console.error('Error fetching admin reservations:', err);
    error.value = 'Error de conexión con la API de administración.';
  } finally {
    loading.value = false;
  }
};

const updateStatus = async (pedidoId: number, newStatus: string) => {
  try {
    const response = await axios.post('http://localhost/fisio_Plenium/api/admin_update_status.php', {
      pedido_id: pedidoId,
      estado: newStatus
    });
    
    if (response.data.success) {
      // Actualizar localmente el estado sin recargar todo
      const index = pedidos.value.findIndex(p => p.id === pedidoId);
      if (index !== -1) {
        pedidos.value[index].estado = newStatus as any;
        showStatus(`Reserva #${pedidoId} marcada como ${newStatus}`);
      }
    } else {
      showStatus('Error: ' + response.data.message, 'danger');
    }
  } catch (err) {
    console.error('Error updating reservation status:', err);
    showStatus('No se pudo actualizar el estado de la reserva.', 'danger');
  }
};

const getStatusBadgeClass = (status: string) => {
  switch (status) {
    case 'confirmado': return 'bg-success';
    case 'cancelado': return 'bg-danger';
    case 'pendiente': return 'bg-warning text-dark';
    default: return 'bg-secondary';
  }
};

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
};

const formatDate = (dateStr: string) => {
  return new Date(dateStr).toLocaleString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

onMounted(fetchReservations);
</script>

<template>
  <div class="container py-5">
    <!-- Full-screen Overlay & Feedback -->
    <Transition name="fade">
      <div v-if="statusMessage" class="feedback-overlay">
        <div 
          class="alert shadow-2xl border-0 px-5 py-4 text-center rounded-4" 
          :class="'alert-' + statusMessage.type"
          style="min-width: 320px; max-width: 500px;"
        >
          <div class="fs-1 mb-2">
            <template v-if="statusMessage.type === 'success'">✅</template>
            <template v-else>❌</template>
          </div>
          <div class="h5 fw-bold mb-0">{{ statusMessage.text }}</div>
        </div>
      </div>
    </Transition>

    <div class="d-flex justify-content-between align-items-center mb-5">
      <div>
        <h1 class="fw-bold mb-0">Panel de Administración</h1>
        <p class="text-muted">Gestiona las reservas y citas de la clínica</p>
      </div>
      <button @click="fetchReservations" class="btn btn-outline-primary btn-sm rounded-pill px-3">
        Actualizar Datos
      </button>
    </div>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
      <p class="mt-2 text-muted text-sm">Cargando reservas...</p>
    </div>

    <div v-else-if="error" class="alert alert-danger shadow-sm">
      {{ error }}
    </div>

    <div v-else-if="pedidos.length === 0" class="text-center py-5">
      <p class="text-muted">No hay ninguna reserva registrada en el sistema.</p>
    </div>



    <div v-else class="table-responsive premium-card p-3 border-0">
      <table class="table table-hover align-middle">
        <thead class="text-muted small text-uppercase">
          <tr>
            <th class="ps-3">ID</th>
            <th>Paciente / Usuario</th>
            <th>Fecha Solicitud</th>
            <th>Servicios</th>
            <th class="text-end">Total</th>
            <th class="text-center">Estado</th>
            <th class="text-end pe-3">Detalle / Acciones</th>
          </tr>
        </thead>
        <tbody>
          <template v-for="p in pedidos" :key="p.id">
            <tr :class="{ 'bg-light': expandedPedidoId === p.id }">
              <td class="ps-3">
                <button @click="toggleRow(p.id)" class="btn btn-sm btn-link text-decoration-none fw-bold p-0">
                  {{ expandedPedidoId === p.id ? '−' : '+' }} #{{ p.id }}
                </button>
              </td>
              <td>
                <div class="fw-bold">{{ p.usuario_nombre || 'Desconocido' }}</div>
                <div class="text-muted small">UID: {{ p.usuario_id }}</div>
              </td>
              <td>{{ formatDate(p.creado_en) }}</td>
              <td>
                <div v-for="l in p.lineas" :key="l.id" class="small text-nowrap">
                  • {{ l.servicio_nombre }} (x{{ l.cantidad }})
                </div>
              </td>
              <td class="text-end fw-bold text-primary">{{ formatCurrency(p.total) }}</td>
              <td class="text-center">
                <span class="badge rounded-pill px-3 py-2" :class="getStatusBadgeClass(p.estado)">
                  {{ p.estado.toUpperCase() }}
                </span>
              </td>
              <td class="text-end pe-3">
                <div class="btn-group btn-group-sm rounded-pill overflow-hidden border">
                  <button 
                    v-if="p.estado !== 'confirmado'" 
                    @click="updateStatus(p.id, 'confirmado')" 
                    class="btn btn-light text-success hover-green"
                    title="Aceptar Reserva"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                    </svg>
                  </button>
                  <button 
                    v-if="p.estado !== 'cancelado'" 
                    @click="updateStatus(p.id, 'cancelado')" 
                    class="btn btn-light text-danger hover-red"
                    title="Rechazar Reserva"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            
            <!-- Expanded Details Row -->
            <tr v-if="expandedPedidoId === p.id">
              <td colspan="7" class="p-0">
                <div class="p-4 bg-light-subtle border-top animate-fade-in">
                  <div class="row">
                    <div class="col-md-3">
                      <h6 class="text-uppercase small fw-bold text-muted mb-3">Contacto Cliente</h6>
                      <p class="mb-1"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>
                        <strong>Fecha Cita:</strong> {{ p.fecha_cita || 'No especificada' }}</p>
                      <p class="mb-1"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M3 2a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V2zm6 11a1 1 0 1 0-2 0 1 1 0 0 0 2 0z"/></svg>
                        <strong>Teléfono:</strong> {{ p.telefono || 'No proporcionado' }}</p>
                      <p class="mb-0 small text-truncate"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z"/></svg>
                        {{ p.usuario_email || 'Sin email' }}</p>
                    </div>
                    <div class="col-md-9 border-start ps-4">
                      <h6 class="text-uppercase small fw-bold text-muted mb-3">Mensaje / Observaciones</h6>
                      <div class="p-3 rounded-3 bg-white border text-dark">
                        {{ p.mensaje || 'Sin observaciones adicionales para esta reserva.' }}
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.table th { border-top: none; }
.badge { font-weight: 600; letter-spacing: 0.5px; font-size: 0.75rem; }
.hover-green:hover { background-color: rgba(25, 135, 84, 0.1) !important; color: #198754 !important; }
.hover-red:hover { background-color: rgba(220, 53, 69, 0.1) !important; color: #dc3545 !important; }

.table-hover tbody tr:hover {
  background-color: rgba(0, 74, 173, 0.02);
}

.feedback-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.15);
  backdrop-filter: blur(4px);
  z-index: 2000;
  pointer-events: none;
}

.feedback-overlay .alert {
  pointer-events: auto;
  animation: scaleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes scaleIn {
  from { transform: scale(0.85); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
</style>
