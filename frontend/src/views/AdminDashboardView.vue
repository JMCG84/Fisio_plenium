<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import type { Pedido } from '../types';

const pedidos = ref<Pedido[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

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
      }
    } else {
      alert('Error al actualizar estado: ' + response.data.message);
    }
  } catch (err) {
    console.error('Error updating reservation status:', err);
    alert('No se pudo actualizar el estado de la reserva.');
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

    <div v-else class="table-responsive bg-white rounded-4 shadow-sm p-3">
      <table class="table table-hover align-middle">
        <thead class="text-muted small text-uppercase">
          <tr>
            <th class="ps-3">ID</th>
            <th>Paciente / Usuario</th>
            <th>Fecha Solicitud</th>
            <th>Servicios</th>
            <th class="text-end">Total</th>
            <th class="text-center">Estado</th>
            <th class="text-end pe-3">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in pedidos" :key="p.id">
            <td class="ps-3 fw-bold text-muted">#{{ p.id }}</td>
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
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped>
.table th { border-top: none; }
.badge { font-weight: 600; letter-spacing: 0.5px; font-size: 0.75rem; }
.hover-green:hover { background-color: #d1e7dd !important; }
.hover-red:hover { background-color: #f8d7da !important; }

.table-hover tbody tr:hover {
  background-color: rgba(0, 74, 173, 0.02);
}
</style>
