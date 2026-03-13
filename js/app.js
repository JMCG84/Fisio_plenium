
//app.js — Se encarga del catalogo de servicios y de mantener el carrito con el localStorage

//Espera a que el documento esté cargado y define el catalago de servicios con un array de objetos 
document.addEventListener('DOMContentLoaded', () => {
  
  const servicios = [
    { id:'fisio-deportiva',      nombre:'Fisioterapia deportiva',      archivo:'fisio-deportiva.jpg',      descripcion:'Rendimiento y recuperación.',           precio:45 },
    { id:'fisio-traumatologica', nombre:'Fisioterapia traumatológica', archivo:'fisio-traumatologica.jpg', descripcion:'Lesiones óseas y articulares.',         precio:50 },
    { id:'fisio-pediatrica',     nombre:'Fisioterapia pediátrica',     archivo:'fisio-pediátrica.jpg',     descripcion:'Atención especializada infantil.',      precio:48 },
    { id:'fisio-respiratoria',   nombre:'Fisioterapia respiratoria',   archivo:'fisio-respiratoria.jpg',   descripcion:'Mejora de la función pulmonar.',        precio:42 },
    { id:'fisio-suelo-pelvico',  nombre:'Suelo pélvico',               archivo:'fisio-suelo Pelvico.jpg',  descripcion:'Rehabilitación del suelo pélvico.',     precio:50 },
    { id:'osteopatia',           nombre:'Osteopatía',                  archivo:'Osteopatia.jpg',           descripcion:'Enfoque manual global.',                precio:55 }
  ];

//Definimos una constante donde guardaremos los datos del carrito
  const CLAVE_CARRITO = 'plenium_carrito';

  // --- Utilidades carrito (localStorage) ---
  
  //Obtener del localStorage el valor guardado en clave_carrito
  function cargarCarrito() {
    try { return JSON.parse(localStorage.getItem(CLAVE_CARRITO)) || {}; }
    catch { return {}; }
  }
  
  //Guarda el objeto carrito en el localstorage
  function guardarCarrito(carrito) {
    localStorage.setItem(CLAVE_CARRITO, JSON.stringify(carrito));
  }
  
  //Carga el carrito agrega los cambios y muestra aviso con el nombre de servicio añadido
  function añadirAlCarrito(serv) {
    const c = cargarCarrito();
    if (!c[serv.id]) c[serv.id] = { nombre: serv.nombre, precio: serv.precio, cantidad: 0 };
    c[serv.id].cantidad++;
    guardarCarrito(c);
    pintarCarrito();
    avisar(`Añadido: ${serv.nombre}`);
  }
  
  //Aumenta en 1 la cantidad del servicio con ese id luego guarda el carrito actualizado y lo repinta
  function sumar(id) {
    const c = cargarCarrito();
    if (!c[id]) return;
    c[id].cantidad++;
    guardarCarrito(c); pintarCarrito();
  }
  
  //Disminuye en 1 la cantidad del servicio con ese id, si llega a 0 o menos elimina el servicio, guarda el carrito y lo actualiza
  function restar(id) {
    const c = cargarCarrito();
    if (!c[id]) return;
    c[id].cantidad--;
    if (c[id].cantidad <= 0) delete c[id];
    guardarCarrito(c); pintarCarrito();
  }
  
  //Elimina directamente el servicio con ese id guarda cambios y actualiza
  function quitar(id) {
    const c = cargarCarrito();
    delete c[id];
    guardarCarrito(c); pintarCarrito();
  }
  
  //Borra el carrito por completo eliminandosu clave del localStorage
  function vaciar() {
    localStorage.removeItem(CLAVE_CARRITO);
    pintarCarrito();
  }
  
  //Devuelve el numero total de articulos del carrito
  function contarArticulos(c) {
    return Object.values(c).reduce((a,it)=>a+(it.cantidad||0),0);
  }
  
  //Devuelve el importe total del carrito 
  function calcularTotal(c) {
    return Object.values(c).reduce((a,it)=>a+(it.cantidad*(+it.precio||0)),0);
  }
  
  //Funcion que devuelve un numero formateado con nuestra moneda el Euro
  const eur = n => new Intl.NumberFormat('es-ES',{style:'currency',currency:'EUR'}).format(n);

  //Busca el elemento con id grid si existe genera la cuadricula de tarjetas a partir del array servicios
  const contenedor = document.getElementById('grid');
  if (contenedor) {
    contenedor.innerHTML = servicios.map(s => `
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <img src="img/${s.archivo}" alt="${s.nombre}" class="card-img-top"
               role="button" tabindex="0"
               data-servicio='${JSON.stringify({id:s.id,nombre:s.nombre,precio:s.precio})}'
               aria-label="Añadir ${s.nombre} al carrito">
          <div class="card-body d-flex flex-column">
            <h3 class="h5 mb-1">${s.nombre}</h3>
            <p class="text-muted mb-3">${s.descripcion}</p>
            <div class="mt-auto d-flex justify-content-between align-items-center">
              <strong>${eur(s.precio)}</strong>
              <button class="btn btn-primary btn-sm"
                      data-servicio='${JSON.stringify({id:s.id,nombre:s.nombre,precio:s.precio})}'>
                Reservar
              </button>
            </div>
          </div>
        </div>
      </div>
    `).join('');
  }

  //Cargar el carrito obtiene referencias del DOM relacionados con el carrito y actualiza el contador de articulos
  function pintarCarrito() {
    const c = cargarCarrito();
    const caja = document.getElementById('cartBox');
    const totalEl = document.getElementById('cartTotal');
    const contador = document.getElementById('cart-count');
    const botonReserva = document.getElementById('btnCheckout');

    if (contador) contador.textContent = contarArticulos(c);

    if (!caja || !totalEl || !botonReserva) return;
	
	
 //Obtiene los elementos del carrito como pares (clave,valor) sino hay muestra mensaje y desahibilia boton de reserva
    const pares = Object.entries(c);
    if (!pares.length) {
      caja.innerHTML = '<p class="text-muted mb-0">Tu carrito está vacío.</p>';
      totalEl.textContent = eur(0);
      botonReserva.setAttribute('aria-disabled','true');
      botonReserva.classList.add('disabled');
      return;
    }
	
	
 //Constuye el html del carrito , se añade el boton vaciar , calcula el total en suma y genera una fila para cada item
    let html = `
      <div class="d-flex justify-content-end mb-2">
        <button id="btn-vaciar" class="btn btn-outline-danger btn-sm" type="button" aria-label="Vaciar carrito">Vaciar</button>
      </div>
    `;
    let suma = 0;
    for (const [id, item] of pares) {
      const subtotal = (item.cantidad||0) * (+item.precio||0);
      suma += subtotal;
      html += `
        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
          <div>
            <strong>${item.nombre}</strong>
            <div class="text-muted small">(${eur(item.precio)} · x${item.cantidad||0})</div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <div class="btn-group btn-group-sm" role="group" aria-label="Cambiar cantidad">
              <button class="btn btn-outline-secondary" data-accion="restar" data-id="${id}" aria-label="Restar uno">−</button>
              <span class="btn btn-light disabled">${item.cantidad||0}</span>
              <button class="btn btn-outline-secondary" data-accion="sumar" data-id="${id}" aria-label="Sumar uno">+</button>
            </div>
            <div class="text-end" style="min-width:90px">${eur(subtotal)}</div>
            <button class="btn btn-sm btn-outline-danger" data-accion="quitar" data-id="${id}" aria-label="Quitar ${item.nombre}">Quitar</button>
          </div>
        </div>
      `;
    }

    caja.innerHTML = html;
    totalEl.textContent = eur(suma);
    botonReserva.removeAttribute('aria-disabled');
    botonReserva.classList.remove('disabled');

    //Si existe le añade funcion al boton Vaciar pide confirmacion al usuario
    const btnVaciar = document.getElementById('btn-vaciar');
    btnVaciar?.addEventListener('click', () => {
      if (confirm('¿Vaciar el carrito completo?')) vaciar();
    });
  }

  //Escucha todos los clics del documento es como una delegación de eventos 
  document.addEventListener('click', (e) => {
    const objetivo = e.target.closest('[data-servicio]');
    if (!objetivo) return;
    e.preventDefault();
    try {
      const datos = JSON.parse(objetivo.getAttribute('data-servicio'));
      añadirAlCarrito(datos);
    } catch {}
  });

  //Escucha cuando se pulsa la tecla enter si el foco esta en un elemento obtiene sus datos y los añade al carrito
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Enter') return;
    const objetivo = e.target.closest('[data-servicio]');
    if (!objetivo) return;
    e.preventDefault();
    try {
      const datos = JSON.parse(objetivo.getAttribute('data-servicio'));
      añadirAlCarrito(datos);
    } catch {}
  });

  //Delegación de eventos para + / − / Quitar (dentro del carrito) ---
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-accion]');
    if (!btn) return;
    const id = btn.getAttribute('data-id');
    const accion = btn.getAttribute('data-accion');
    if (!id || !accion) return;

    if (accion === 'sumar')  sumar(id);
    if (accion === 'restar') restar(id);
    if (accion === 'quitar') quitar(id);
  });

  //Aviso visual en esquina inferior derecha lo muestra en pantalla y lo elimina automaticamente
  function avisar(mensaje) {
    const aviso = document.createElement('div');
    aviso.textContent = mensaje;
    aviso.style.cssText = `
      position:fixed; right:16px; bottom:16px; z-index:1056;
      background:#198754; color:#fff; padding:10px 14px; border-radius:10px;
      box-shadow:0 6px 20px rgba(0,0,0,.2); font-size:14px;
    `;
    document.body.appendChild(aviso);
    setTimeout(()=>aviso.remove(), 1400);
  }

  //Muestra el estado inicial del carrito al cargar la pagina y cierra el DOMContentLoaded
  pintarCarrito();
});
