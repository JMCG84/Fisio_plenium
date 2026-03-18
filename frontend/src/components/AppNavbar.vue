<script setup lang="ts">
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';
import { useRouter } from 'vue-router';
import { storeToRefs } from 'pinia';
import { ref, onMounted } from 'vue';

const cartStore = useCartStore();
const authStore = useAuthStore();
const router = useRouter();

const isDarkMode = ref(false);

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  if (isDarkMode.value) {
    document.body.classList.add('dark-mode');
    document.body.setAttribute('data-bs-theme', 'dark');
    localStorage.setItem('theme', 'dark');
  } else {
    document.body.classList.remove('dark-mode');
    document.body.removeAttribute('data-bs-theme');
    localStorage.setItem('theme', 'light');
  }
};

onMounted(() => {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDarkMode.value = true;
    document.body.classList.add('dark-mode');
    document.body.setAttribute('data-bs-theme', 'dark');
  }
});

const handleLogout = () => {
  authStore.logout();
  router.push({ name: 'home' });
};

const { totalCount } = storeToRefs(cartStore);
</script>

<template>
  <nav class="navbar navbar-expand-lg glass-nav py-3">
    <div class="container">
      <router-link to="/" class="navbar-brand fw-bold d-flex align-items-center gap-2">
        <img src="/img/logo.jpg" alt="Logo Plenium" width="40" height="40" class="rounded-circle object-fit-cover shadow-sm" />
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
            <button @click="toggleDarkMode" class="btn btn-outline-secondary d-flex align-items-center gap-2 p-2 rounded-circle" aria-label="Cambiar tema">
              <svg v-if="!isDarkMode" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path d="M6 .278a.77.77 0 0 1 .08.858 7.2 7.2 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277q.792-.001 1.533-.16a.79.79 0 0 1 .81.316.73.73 0 0 1-.031.893A8.35 8.35 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.75.75 0 0 1 6 .278"/>
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6m0 1a4 4 0 1 0 0-8 4 4 0 0 0 0 8M8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0m0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13m8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5M3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8m10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0m-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0m9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707M4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708"/>
              </svg>
            </button>
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
