import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';
import type { Servicio, CartItem } from '../types';

export const useCartStore = defineStore('cart', () => {
  const items = ref<Record<string, CartItem>>(
    JSON.parse(localStorage.getItem('plenium_carrito') || '{}')
  );

  const totalCount = computed(() => 
    Object.values(items.value).reduce((acc, item) => acc + item.cantidad, 0)
  );

  const totalAmount = computed(() => 
    Object.values(items.value).reduce((acc, item) => acc + (item.precio * item.cantidad), 0)
  );

  const addService = (servicio: Servicio) => {
    if (items.value[servicio.id]) {
      items.value[servicio.id].cantidad++;
    } else {
      items.value[servicio.id] = { ...servicio, cantidad: 1 };
    }
  };

  const removeOne = (id: string) => {
    if (items.value[id]) {
      items.value[id].cantidad--;
      if (items.value[id].cantidad <= 0) {
        delete items.value[id];
      }
    }
  };

  const removeAll = (id: string) => {
    delete items.value[id];
  };

  const clearCart = () => {
    items.value = {};
  };

  watch(items, (newItems) => {
    localStorage.setItem('plenium_carrito', JSON.stringify(newItems));
  }, { deep: true });

  return {
    items,
    totalCount,
    totalAmount,
    addService,
    removeOne,
    removeAll,
    clearCart
  };
});
