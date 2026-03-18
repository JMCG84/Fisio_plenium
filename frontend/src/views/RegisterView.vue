<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();

const nombre = ref("");
const email = ref("");
const password = ref("");
const loading = ref(false);
const error = ref("");
const successMessage = ref("");
const errors = ref({ nombre: "", email: "", password: "" });

const validateForm = () => {
  let valid = true;
  errors.value = { nombre: "", email: "", password: "" };

  if (!nombre.value.trim()) { errors.value.nombre = "El nombre es obligatorio"; valid = false; }
  if (!email.value.trim()) { errors.value.email = "El correo es obligatorio"; valid = false; }
  else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) { errors.value.email = "Correo no válido"; valid = false; }
  if (!password.value) { errors.value.password = "La contraseña es obligatoria"; valid = false; }
  else if (password.value.length < 6) { errors.value.password = "Debe tener al menos 6 caracteres"; valid = false; }

  return valid;
};

const handleRegister = async () => {
  error.value = "";
  successMessage.value = "";
  
  if (!validateForm()) return;

  loading.value = true;
  try {
    const response = await axios.post(
      "http://localhost/fisio_Plenium/api/register.php",
      {
        nombre: nombre.value,
        email: email.value,
        password: password.value,
      },
    );

    if (response.data.success) {
      successMessage.value =
        "Registro exitoso. Redirigiendo al inicio de sesión...";
      setTimeout(() => {
        router.push({ name: "login" });
      }, 2000);
    } else {
      error.value = response.data.message;
    }
  } catch (err) {
    console.error("Register error:", err);
    error.value = "Error de conexión con el servidor.";
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="container py-5 d-flex justify-content-center">
    <div class="premium-card p-5 w-100" style="max-width: 500px">
      <h2 class="fw-bold mb-4 text-center">Crea tu Cuenta</h2>

      <div v-if="error" class="alert alert-danger small py-2 mb-4">
        {{ error }}
      </div>
      <div v-if="successMessage" class="alert alert-success small py-2 mb-4">
        {{ successMessage }}
      </div>

      <form @submit.prevent="handleRegister" novalidate>
        <div class="mb-3">
          <label class="form-label small fw-bold text-muted"
            >Nombre Completo</label
          >
          <input
            v-model="nombre"
            type="text"
            class="form-control form-control-lg border-2"
            :class="{ 'is-invalid border-danger': errors.nombre }"
            placeholder="Juan Pérez"
          />
          <span v-if="errors.nombre" class="text-danger small mt-1 d-block">{{ errors.nombre }}</span>
        </div>
        <div class="mb-3">
          <label class="form-label small fw-bold text-muted"
            >Correo Electrónico</label
          >
          <input
            v-model="email"
            type="email"
            class="form-control form-control-lg border-2"
            :class="{ 'is-invalid border-danger': errors.email }"
            placeholder="tu@email.com"
          />
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
          />
          <span v-if="errors.password" class="text-danger small mt-1 d-block">{{ errors.password }}</span>
        </div>
        <button
          type="submit"
          class="btn btn-premium w-100 py-3 mb-3"
          :disabled="loading"
        >
          <span
            v-if="loading"
            class="spinner-border spinner-border-sm me-2"
          ></span>
          {{ loading ? "Registrando..." : "Registrarse Ahora" }}
        </button>
        <div class="text-center">
          <span class="text-muted small">¿Ya tienes cuenta?</span>
          <router-link
            to="/login"
            class="ms-2 small fw-bold text-primary text-decoration-none"
            >Inicia sesión</router-link
          >
        </div>
      </form>
    </div>
  </div>
</template>
