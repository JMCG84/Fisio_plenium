<script setup lang="ts">
import { useCartStore } from '../stores/cart';
import { storeToRefs } from 'pinia';
import { ref } from 'vue';

import axios from 'axios';

const cartStore = useCartStore();
const { items, totalAmount } = storeToRefs(cartStore);

const form = ref({
  nombre: '',
  email: '',
  telefono: '',
  fecha: '',
  mensaje: ''
});

const isSubmitting = ref(false);
const submitSuccess = ref(false);
const submitError = ref('');
const errors = ref({ nombre: '', email: '', telefono: '', fecha: '' });

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
};

const validateForm = () => {
  let valid = true;
  errors.value = { nombre: '', email: '', telefono: '', fecha: '' };
  
  if (!form.value.nombre.trim()) { errors.value.nombre = 'El nombre es obligatorio'; valid = false; }
  if (!form.value.email.trim()) { errors.value.email = 'El correo electrónico es obligatorio'; valid = false; }
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) { errors.value.email = 'El correo electrónico no es válido'; valid = false; }
  if (!form.value.telefono.trim()) { errors.value.telefono = 'El teléfono es obligatorio'; valid = false; }
  if (!form.value.fecha.trim()) { errors.value.fecha = 'La fecha preferida es obligatoria'; valid = false; }
  
  return valid;
};

const handleSubmit = async () => {
  submitSuccess.value = false;
  submitError.value = '';
  
  if (!validateForm()) return;
  
  if (Object.keys(items.value).length === 0) return;
  
  isSubmitting.value = true;
  try {
    const payload = {
      ...form.value,
      total: totalAmount.value,
      items: Object.values(items.value)
    };
    
    const response = await axios.post('http://localhost/fisio_Plenium/api/reservations.php', payload);
    
    if (response.data.success) {
      submitSuccess.value = true;
      cartStore.clearCart();
      form.value = { nombre: '', email: '', telefono: '', fecha: '', mensaje: '' };
    } else {
      submitError.value = 'Error: ' + response.data.message;
    }
  } catch (err) {
    console.error('Error saving reservation:', err);
    submitError.value = 'No se pudo conectar con el servidor para guardar la reserva.';
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <div class="container py-5">
    <div class="row g-5">
      <!-- Checkout Form -->
      <div class="col-lg-7">
        <div class="premium-card p-4 p-md-5">
          <h2 class="fw-bold mb-4">Solicitar Reserva</h2>
          
          <div v-if="submitSuccess" class="alert alert-success fw-bold text-center border-0 shadow-sm">
            ✅ Reserva enviada de manera correcta. El administrador le responderá.
          </div>
          <div v-if="submitError" class="alert alert-danger shadow-sm">
            {{ submitError }}
          </div>

          <form v-if="!submitSuccess" @submit.prevent="handleSubmit" novalidate>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Nombre Completo</label>
                <input v-model="form.nombre" type="text" class="form-control form-control-lg border-2" :class="{ 'is-invalid border-danger': errors.nombre }" placeholder="Juan Pérez">
                <span v-if="errors.nombre" class="text-danger small mt-1 d-block">{{ errors.nombre }}</span>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Correo Electrónico</label>
                <input v-model="form.email" type="email" class="form-control form-control-lg border-2" :class="{ 'is-invalid border-danger': errors.email }" placeholder="juan@ejemplo.com">
                <span v-if="errors.email" class="text-danger small mt-1 d-block">{{ errors.email }}</span>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Teléfono</label>
                <input v-model="form.telefono" type="tel" class="form-control form-control-lg border-2" :class="{ 'is-invalid border-danger': errors.telefono }" placeholder="600 000 000">
                <span v-if="errors.telefono" class="text-danger small mt-1 d-block">{{ errors.telefono }}</span>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-muted">Fecha preferida</label>
                <input v-model="form.fecha" type="date" class="form-control form-control-lg border-2" :class="{ 'is-invalid border-danger': errors.fecha }">
                <span v-if="errors.fecha" class="text-danger small mt-1 d-block">{{ errors.fecha }}</span>
              </div>
              <div class="col-12">
                <label class="form-label small fw-bold text-muted">Observaciones (opcional)</label>
                <textarea v-model="form.mensaje" class="form-control border-2" rows="3" placeholder="Padecimientos recientes, dudas..."></textarea>
              </div>
            </div>
            
            <button type="submit" class="btn btn-premium w-100 py-3 mt-4 fs-5" :disabled="isSubmitting || Object.keys(items).length === 0">
              <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
              {{ isSubmitting ? 'Procesando...' : 'Confirmar Solicitud' }}
            </button>
          </form>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="col-lg-5">
        <div class="premium-card p-4 bg-light border-0 shadow-none">
          <h4 class="fw-bold mb-4">Resumen de Sesiones</h4>
          
          <div v-if="Object.keys(items).length === 0" class="text-center py-5">
            <p class="text-muted">No has seleccionado ningún servicio.</p>
            <router-link to="/" class="btn btn-outline-primary">Ver Catálogo</router-link>
          </div>

          <div v-else>
            <div v-for="item in items" :key="item.id" class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <span class="fw-bold d-block">{{ item.nombre }}</span>
                <small class="text-muted">Cantidad: {{ item.cantidad }}</small>
              </div>
              <span class="fw-medium">{{ formatCurrency(item.precio * item.cantidad) }}</span>
            </div>
            
            <div class="border-top pt-3 mt-4">
              <div class="d-flex justify-content-between align-items-center">
                <span class="h5 fw-bold mb-0">Total a pagar</span>
                <span class="h4 fw-bold gradient-text mb-0">{{ formatCurrency(totalAmount) }}</span>
              </div>
            </div>

            <div class="alert alert-info border-0 mt-4 small">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
              </svg>
              El pago se realizará en la clínica tras la sesión.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.form-control:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 0.25rem rgba(0, 74, 173, 0.1);
}
</style>
