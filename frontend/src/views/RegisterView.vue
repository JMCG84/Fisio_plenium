<template>
  <div class="container py-5 d-flex justify-content-center">
    <div class="premium-card p-5 w-100" style="max-width: 500px;">
      <h2 class="fw-bold mb-4 text-center">Crea tu Cuenta</h2>
      <form>
        <div class="mb-3">
          <label class="form-label small fw-bold text-muted">Nombre Completo</label>
          <input type="text" class="form-control form-control-lg border-2" placeholder="Juan Pérez">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-bold text-muted">Correo Electrónico</label>
          <input type="email" class="form-control form-control-lg border-2" placeholder="tu@email.com">
        </div>
        <div class="mb-4">
          <label class="form-label small fw-bold text-muted">Contraseña</label>
          <input type="password" class="form-control form-control-lg border-2" placeholder="••••••••">
        </div>
        <button type="submit" class="btn btn-premium w-100 py-3 mb-3">Registrarse Ahora</button>
        <div class="text-center">
          <span class="text-muted small">¿Ya tienes cuenta?</span>
          <router-link to="/login" class="ms-2 small fw-bold text-primary text-decoration-none">Inicia sesión</router-link>
        </div>
      </form>
    </div>
  </div>
</template>
