<script setup lang="ts">
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';

const cartStore = useCartStore();
const authStore = useAuthStore();
const router = useRouter();

const handleLogout = () => {
  authStore.logout();
  router.push({ name: 'home' });
};

const { totalCount } = storeToRefs(cartStore);
</script>

<template>
  <nav class="navbar navbar-expand-lg glass-nav py-3">
    <div class="container">
      <router-link to="/" class="navbar-brand fw-bold d-flex align-items-center">
        <span class="gradient-text fs-3">Plenium</span>
      </router-link>
      
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navMain">
        <ul class="navbar-nav ms-auto align-items-center gap-2">
          <li class="nav-item">
            <router-link to="/" class="nav-link px-3">Inicio</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/reserva" class="nav-link px-3">Reservar</router-link>
          </li>
          <li v-if="authStore.isAdmin" class="nav-item">
            <router-link to="/admin" class="nav-link px-3 text-primary-emphasis fw-bold">Admin</router-link>
          </li>
          <li v-if="!authStore.isAuthenticated" class="nav-item">
            <router-link to="/login" class="nav-link px-3">Iniciar Sesión</router-link>
          </li>
          <li v-else class="nav-item dropdown">
            <a class="nav-link dropdown-toggle px-3" href="#" role="button" data-bs-toggle="dropdown">
              Hola, {{ authStore.user?.nombre }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end premium-card border-0 shadow-sm mt-2">
              <li><button @click="handleLogout" class="dropdown-item text-danger small py-2">Cerrar Sesión</button></li>
            </ul>
          </li>
          <li class="nav-item ms-lg-2">
            <button 
              class="btn btn-premium d-flex align-items-center gap-2"
              data-bs-toggle="offcanvas" 
              data-bs-target="#offCart"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
              </svg>
              <span>Carrito</span>
              <span class="badge bg-white text-primary rounded-pill shadow-sm">{{ totalCount }}</span>
            </button>
          </li>
          <li class="nav-item ms-lg-2">
            <router-link to="/login" class="nav-link font-semibold text-primary">Iniciar Sesión</router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<style scoped>
.navbar-brand {
  letter-spacing: -1px;
}

.nav-link {
  font-weight: 500;
  color: var(--text);
  transition: color 0.2s;
}

.nav-link:hover, .router-link-active {
  color: var(--primary);
}
</style>
