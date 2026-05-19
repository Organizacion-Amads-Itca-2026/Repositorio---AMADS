const menuComponent = () => import('src/layouts/integraciones-vistas/menu-principal.vue')

const routes = [
  {
    path: '/',
    children: [{ path: '', component: () => import('src/pages/General/pagina-login.vue') }],
    meta: {
      title: 'Inicio de Sesión',
      requiresAuth: false,
    },
  },

  {
    path: '/registro',
    children: [{ path: '', component: () => import('src/pages/General/pagina-registro.vue') }],
    meta: { 
      title: 'Crear Cuenta', 
      requiresAuth: false 
    },
  },

  // CERRAR SESION
  {
    path: '/cerrar-sesion',
    component: () => import('src/components/componentes-generales/cerrar-sesion.vue'),
    meta: {
      title: 'Cerrar Sesión',
      requiresAuth: true,
    },
  },

  // MENU DE INICIO
  {
    path: '/inicio',
    component: menuComponent,
    children: [{ path: '', component: () => import('src/pages/General/pagina-principal.vue') }],
    meta: {
      title: 'Menú Principal',
      requiresAuth: true,
      roles: ['ADMINISTRADOR'], 
    },
  },
  {
    path: '/inicio-usuarios',
    component: menuComponent,
    children: [
      { path: '', component: () => import('src/pages/General/pagina-principal-usuarioC.vue') },
    ],
    meta: {
      title: 'Bienvenidos',
      requiresAuth: true, 
      roles: ['USUARIO'],
    },
  },

  {
    path: '/eventos',
    component: menuComponent,
    children: [{ path: '', component: () => import('src/pages/Comercial/administrar-eventos.vue') }],
    meta: {
      title: 'Administrar Eventos',
      requiresAuth: true,
      roles: ['ADMIN', 'ADMINISTRADOR', 'VENDEDOR'],
    },
  },
  {
    path: '/proveedores',
    component: menuComponent,
    children: [
      { path: '', component: () => import('src/pages/Comercial/administrar-proveedores.vue') },
    ],
    meta: {
      title: 'Administrar Proveedores',
      requiresAuth: true,
      roles: ['ADMIN', 'ADMINISTRADOR'],
    },
  },
  {
    path: '/clientes',
    component: menuComponent,
    children: [
      { path: '', component: () => import('src/pages/Comercial/administrar-clientes.vue') },
    ],
    meta: {
      title: 'Administrar Clientes',
      requiresAuth: true,
      roles: ['ADMIN', 'ADMINISTRADOR', 'VENDEDOR'],
    },
  },
 
 
  {
    path: '/descuentos',
    component: menuComponent,
    children: [
      { path: '', component: () => import('src/pages/Comercial/administrar-descuentos.vue') },
    ],
    meta: {
      title: 'Administrar Descuentos',
      requiresAuth: true,
      roles: ['ADMIN', 'ADMINISTRADOR'],
    },
  },
  {
    path: '/categorias-tickets',
    component: menuComponent,
    children: [
      { path: '', component: () => import('src/pages/Comercial/categorias-tickets.vue') },
    ],
    meta: {
      title: 'Categorías de Tickets',
      requiresAuth: true,
      roles: ['ADMIN', 'ADMINISTRADOR'],
    },
  },

  // CONFIGURACION
  {
    path: '/lugares-evento',
    component: menuComponent,
    children: [{ path: '', component: () => import('src/pages/Configuracion/lugares-evento.vue') }],
    meta: {
      title: 'Lugares de Eventos',
      requiresAuth: true,
      roles: ['ADMIN', 'ADMINISTRADOR'],
    },
  },
  {
    path: '/tipos-evento',
    component: menuComponent,
    children: [{ path: '', component: () => import('src/pages/Configuracion/tipos-evento.vue') }],
    meta: {
      title: 'Tipos de Eventos',
      requiresAuth: true,
      roles: ['ADMIN', 'ADMINISTRADOR'],
    },
  },
  {
    path: '/administrar-empleados',
    component: menuComponent,
    children: [
      { path: '', component: () => import('src/pages/Configuracion/AdministrarEmpleados.vue') },
    ],
    meta: {
      title: 'Administrar Empleados',
      requiresAuth: true,
      roles: ['ADMIN', 'ADMINISTRADOR'],
    },
  },

  
  {
    path: '/cambiar-clave',
    component: menuComponent,
    children: [{ path: '', component: () => import('src/pages/Seguridad/cambiar-clave.vue') }],
    meta: {
      title: 'Cambiar Clave',
      requiresAuth: true,
    },
  },
  {
    path: '/crear-usuario',
    component: menuComponent,
    children: [{ path: '', component: () => import('src/pages/Seguridad/crear-usuario.vue') }],
    meta: {
      title: 'Crear Usuario',
      requiresAuth: true,
      roles: ['ADMIN', 'ADMINISTRADOR'],
    },
  },
  {
    path: '/administrar-usuarios',
    component: menuComponent,
    children: [
      { path: '', component: () => import('src/pages/Seguridad/administrar-usuarios.vue') },
    ],
    meta: {
      title: 'Administrar Usuarios',
      requiresAuth: true,
      roles: ['ADMIN', 'ADMINISTRADOR'],
    },
  },



  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('src/pages/General/pagina-no-encontrada.vue'),
  },
]

export default routes
