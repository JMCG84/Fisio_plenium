<script setup lang="ts">
import { RouterView } from 'vue-router';
import AppNavbar from './components/AppNavbar.vue';
import CartSidebar from './components/CartSidebar.vue';
</script>

<template>
  <div class="app-container">
    <AppNavbar />
    
    <main>
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <CartSidebar />
    
    <footer class="py-5 mt-5 bg-dark text-light">
      <div class="container text-center">
        <p class="mb-1">© 2025 Clínica de Fisioterapia Plenium</p>
        <p class="text-muted small">Equilibramos tu salud para una vida en plenitud</p>
      </div>
    </footer>
  </div>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.app-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
}
</style>
