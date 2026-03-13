import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { Usuario } from '../types';

export const useAuthStore = defineStore('auth', () => {
  const user = ref<Usuario | null>(
    JSON.parse(localStorage.getItem('plenium_user') || 'null')
  );

  const isAuthenticated = computed(() => user.value !== null);
  const isAdmin = computed(() => user.value?.rol === 'admin');

  const setUser = (userData: Usuario) => {
    user.value = userData;
    localStorage.setItem('plenium_user', JSON.stringify(userData));
  };

  const logout = () => {
    user.value = null;
    localStorage.removeItem('plenium_user');
  };

  return {
    user,
    isAuthenticated,
    isAdmin,
    setUser,
    logout
  };
});
