export interface Servicio {
  id: number;
  nombre: string;
  descripcion: string;
  archivo: string;
  precio: number;
}

export interface CartItem extends Servicio {
  cantidad: number;
}

export interface PedidoLinea {
  id: number;
  pedido_id: number;
  servicio_id: number;
  cantidad: number;
  precio_u: number;
  servicio_nombre?: string;
}

export interface Pedido {
  id: number;
  usuario_id: number;
  total: number;
  estado: 'pendiente' | 'confirmado' | 'cancelado';
  creado_en: string;
  usuario_nombre?: string;
  lineas?: PedidoLinea[];
}

export interface Usuario {
  id: number;
  nombre: string;
  email: string;
  rol: 'paciente' | 'admin';
}
