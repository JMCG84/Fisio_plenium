<script setup lang="ts">
import { useCartStore } from '../stores/cart';
import { storeToRefs } from 'pinia';
import { useRouter } from 'vue-router';

const cartStore = useCartStore();
const { items, totalAmount, totalCount } = storeToRefs(cartStore);
const router = useRouter();

const goToReserva = () => {
  // Let Bootstrap begin offcanvas exit transition, then route
  setTimeout(() => {
    router.push({ name: 'reserva' });
  }, 300);
};

const formatCurrency = (value: number | string) => {
  return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(Number(value));
};
</script>

<template>
  <div class="offcanvas offcanvas-end premium-offcanvas" tabindex="-1" id="offCart">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title fw-bold" id="offCartLabel">
        Tu Carrito <span class="badge bg-primary-subtle text-primary rounded-pill ms-2">{{ totalCount }}</span>
      </h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-4">
      <div v-if="Object.keys(items).length === 0" class="flex-grow-1 d-flex flex-column align-items-center justify-content-center text-center">
        <div class="empty-cart-icon mb-3">🛒</div>
        <p class="text-muted">Tu carrito está vacío.<br>Agregue sesiones para comenzar.</p>
      </div>
      
      <div v-else class="flex-grow-1 overflow-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <span class="text-muted small">Servicio seleccionado</span>
          <button @click="cartStore.clearCart" class="btn btn-link btn-sm text-danger p-0 text-decoration-none">Vaciar Todo</button>
        </div>

        <div v-for="item in items" :key="item.id" class="cart-item-row mb-3 p-3 rounded-4 bg-light shadow-sm">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="fw-bold mb-0">{{ item.nombre }}</h6>
            <button @click="cartStore.removeAll(item.id)" class="btn-close small" style="font-size: 0.6rem;"></button>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="input-group input-group-sm w-auto">
              <button @click="cartStore.removeOne(item.id)" class="btn btn-outline-primary border-2 px-3">-</button>
              <span class="px-3 py-1 bg-white border-top border-bottom border-primary border-2 fw-bold">{{ item.cantidad }}</span>
              <button @click="cartStore.addService(item)" class="btn btn-outline-primary border-2 px-3">+</button>
            </div>
            <span class="fw-bold text-primary">{{ formatCurrency(item.precio * item.cantidad) }}</span>
          </div>
        </div>
      </div>

      <div class="border-top pt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-muted fw-medium">Precio Total</span>
          <span class="fs-4 fw-bold gradient-text">{{ formatCurrency(totalAmount) }}</span>
        </div>
        <button 
          @click="goToReserva"
          class="btn btn-premium w-100 py-3 mt-2" 
          :disabled="totalCount === 0"
          data-bs-dismiss="offcanvas"
        >
          Confirmar Reserva
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.premium-offcanvas {
  border-left: none;
  box-shadow: -10px 0 30px rgba(0,0,0,0.05);
}

.empty-cart-icon {
  font-size: 3rem;
  opacity: 0.3;
}

.cart-item-row {
  transition: transform 0.2s;
}

.cart-item-row:hover {
  transform: translateX(-4px);
}
</style>
