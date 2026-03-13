<script setup lang="ts">
import type { Servicio } from '../types';
import { useCartStore } from '../stores/cart';

const props = defineProps<{
  servicio: Servicio;
}>();

const cartStore = useCartStore();

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
};
</script>

<template>
  <div class="col-12 col-md-6 col-lg-4 animate-fade-in">
    <div class="card h-100 premium-card p-2">
      <div class="image-wrapper mb-3">
        <img :src="`/img/${servicio.archivo}`" :alt="servicio.nombre" class="card-img-top rounded-4">
        <div class="price-badge">{{ formatCurrency(servicio.precio) }}</div>
      </div>
      <div class="card-body d-flex flex-column pt-0">
        <h3 class="h5 fw-bold mb-2">{{ servicio.nombre }}</h3>
        <p class="text-muted small flex-grow-1">{{ servicio.descripcion }}</p>
        <div class="mt-3 d-grid">
          <button @click="cartStore.addService(servicio)" class="btn btn-premium btn-sm">
            Reservar Sesión
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.image-wrapper {
  position: relative;
  overflow: hidden;
  border-radius: 14px;
}

.card-img-top {
  height: 200px;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.premium-card:hover .card-img-top {
  transform: scale(1.1);
}

.price-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  background: var(--glass);
  backdrop-filter: blur(8px);
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 700;
  color: var(--primary);
  font-size: 0.9rem;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
</style>
