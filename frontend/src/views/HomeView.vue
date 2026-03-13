<script setup lang="ts">
import { ref, onMounted } from 'vue';
import axios from 'axios';
import ServiceCard from '../components/ServiceCard.vue';
import type { Servicio } from '../types';

const servicios = ref<Servicio[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

const fetchServices = async () => {
  try {
    const response = await axios.get('http://localhost/fisio_Plenium/api/services.php');
    if (response.data.success) {
      servicios.value = response.data.data;
      error.value = null;
    } else {
      throw new Error();
    }
  } catch (err) {
    console.warn('Backend no disponible. Cargando modo demo local.');
    // Fallback: Datos reales del proyecto para que el portfolio siempre funcione
    servicios.value = [
      { id: 1, nombre:'Fisioterapia deportiva',      archivo:'fisio-deportiva.jpg',      descripcion:'Optimiza tu rendimiento y acelera tu recuperación post-entrenamiento.',           precio:45 },
      { id: 2, nombre:'Fisioterapia traumatológica', archivo:'fisio-traumatologica.jpg', descripcion:'Tratamiento integral de lesiones óseas, articulares y de tejidos blandos.',         precio:50 },
      { id: 3, nombre:'Fisioterapia pediátrica',     archivo:'fisio-pediátrica.jpg',     descripcion:'Atención especializada en el desarrollo motor infantil y cólicos.',      precio:48 },
      { id: 4, nombre:'Fisioterapia respiratoria',   archivo:'fisio-respiratoria.jpg',   descripcion:'Mejora la función pulmonar y reduce la fatiga respiratoria.',        precio:42 },
      { id: 5, nombre:'Suelo pélvico',               archivo:'fisio-suelo Pelvico.jpg',  descripcion:'Rehabilitación postparto y tratamiento de disfunciones pélvicas.',     precio:50 },
      { id: 6, nombre:'Osteopatía',                  archivo:'Osteopatia.jpg',           descripcion:'Enfoque manual global enfocado en restaurar el equilibrio corporal.',                precio:55 }
    ];
    error.value = "Conexión limitada: La base de datos no responde, cargando catálogo local de reserva.";
  } finally {
    loading.value = false;
  }
};

onMounted(fetchServices);
</script>

<template>
  <div class="home">
    <!-- Hero Section -->
    <header class="hero-section py-5 mb-5 text-center bg-white">
      <div class="container py-lg-5">
        <h1 class="display-3 fw-bold mb-3 animate-fade-in shadow-text">
          Tu Salud en <span class="gradient-text">Plenitud</span>
        </h1>
        <p class="lead text-muted mb-4 mx-auto animate-fade-in" style="max-width: 600px; animation-delay: 0.2s">
          Especialistas en fisioterapia avanzada. Reserva tu sesión hoy y siente la diferencia de un cuidado personalizado.
        </p>
        <div class="tagline-badge animate-fade-in" style="animation-delay: 0.4s">
          Equilibramos tu bienestar
        </div>
      </div>
    </header>

    <!-- Services Grid -->
    <section class="container mb-5">
      <div class="row align-items-center mb-4 px-3">
        <div class="col">
          <h2 class="h3 fw-bold mb-0">Nuestros Servicios</h2>
          <p class="text-muted small">Cuidado especializado para cada necesidad</p>
        </div>
      </div>
      
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
        <p class="mt-2 text-muted">Cargando catálogo...</p>
      </div>

      <div v-else-if="error" class="alert alert-danger mx-3">
        {{ error }}
      </div>

      <div v-else class="row g-4 px-3">
        <ServiceCard 
          v-for="servicio in servicios" 
          :key="servicio.id" 
          :servicio="servicio" 
        />
      </div>
    </section>
  </div>
</template>

<style scoped>
.hero-section {
  background: radial-gradient(circle at 10% 20%, rgba(0, 74, 173, 0.03) 0%, rgba(255, 255, 255, 1) 90%);
}

.shadow-text {
  text-shadow: 0 4px 10px rgba(0, 74, 173, 0.05);
}

.tagline-badge {
  display: inline-block;
  padding: 8px 16px;
  background: var(--primary-dark);
  color: white;
  border-radius: 50px;
  font-size: 0.85rem;
  font-weight: 600;
  letter-spacing: 1px;
  text-transform: uppercase;
}
</style>
