<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');
const errors = ref({ email: '', password: '' });

const validateForm = () => {
  let valid = true;
  errors.value = { email: '', password: '' };

  if (!email.value.trim()) { errors.value.email = 'El correo electrónico es obligatorio'; valid = false; }
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) { errors.value.email = 'Correo no válido'; valid = false; }
  if (!password.value) { errors.value.password = 'La contraseña es obligatoria'; valid = false; }

  return valid;
};

const handleLogin = async () => {
  error.value = '';
  
  if (!validateForm()) return;
  
  loading.value = true;
  try {
    const response = await axios.post('http://localhost/fisio_Plenium/api/login.php', {
      email: email.value,
      password: password.value
    });

    if (response.data.success) {
      authStore.setUser(response.data.user);
      if (authStore.isAdmin) {
        router.push({ name: 'admin' });
      } else {
        router.push({ name: 'home' });
      }
    } else {
      error.value = response.data.message;
    }
  } catch (err) {
    console.error('Login error:', err);
    error.value = 'Error de conexión con el servidor.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="container py-5 d-flex justify-content-center">
    <div class="premium-card p-5 w-100" style="max-width: 450px;">
      <h2 class="fw-bold mb-4 text-center">Iniciar Sesión</h2>
      
      <div v-if="error" class="alert alert-danger small py-2 mb-4">
        {{ error }}
      </div>

      <form @submit.prevent="handleLogin" novalidate>
        <div class="mb-3">
          <label class="form-label small fw-bold text-muted">Correo Electrónico</label>
          <input 
            v-model="email" 
            type="email" 
            class="form-control form-control-lg border-2" 
            :class="{ 'is-invalid border-danger': errors.email }"
            placeholder="tu@email.com" 
          >
          <span v-if="errors.email" class="text-danger small mt-1 d-block">{{ errors.email }}</span>
        </div>
        <div class="mb-4">
          <label class="form-label small fw-bold text-muted">Contraseña</label>
          <input 
            v-model="password" 
            type="password" 
            class="form-control form-control-lg border-2" 
            :class="{ 'is-invalid border-danger': errors.password }"
            placeholder="••••••••" 
          >
          <span v-if="errors.password" class="text-danger small mt-1 d-block">{{ errors.password }}</span>
        </div>
        <button type="submit" class="btn btn-premium w-100 py-3 mb-3" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
          {{ loading ? 'Entrando...' : 'Entrar' }}
        </button>
        <div class="text-center">
          <span class="text-muted small">¿No tienes cuenta?</span>
          <router-link to="/register" class="ms-2 small fw-bold text-primary text-decoration-none">Regístrate</router-link>
        </div>
      </form>
    </div>
  </div>
</template>
