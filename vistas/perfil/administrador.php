<title>Perfil Administrador</title>

<link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/checkout/">

<link href="RUPE/assets/css/bootstrap.min.css" rel="stylesheet">

<style>
    .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
    }

    @media (min-width: 768px) {
        .bd-placeholder-img-lg {
            font-size: 3.5rem;
        }
    }
</style>

<main>
    <section class="py-5">
        <div class="py-4">
            <div class="p-2 mb-2 rounded-3" style="background-color: #90B6CE;">
                <div class="container-fluid">
                    <h5 class="display-8 fw-bold">PERFIL ADMINISTRADOR</h5>
                </div>
            </div>

            <div class="row align-items-md-stretch">
                <div class="col-md-6">
                    <div class="h-100 p-3 bg-light border rounded-3">

                        <!-- una fila interna para dividir la tarjeta -->
                        <div class="row align-items-center h-100">

                            <!-- Imagen -->
                            <div class="col-sm-4 text-center mb-3 mb-sm-0">
                                <img src="<?= htmlspecialchars(
                                                $_SESSION['admin_foto']
                                                    ?? '/RUPE/assets/fotos/avatar-de-perfil.png'
                                            ); ?>"
                                    alt="Foto de perfil"
                                    class="img-fluid rounded-circle border shadow-sm"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                            </div>

                            <!-- Información -->
                            <div class="col-sm-8">
                                <h2>Datos personales</h2>
                                <p class="text-muted"><?= htmlspecialchars(
                                                            $_SESSION['admin_nombre']
                                                                ?? 'Administrador'
                                                        ); ?></p>
                                <div class="d-grid gap-2 d-md-block">
                                    <a href="/RUPE/controladores/personaladmin.php" class="btn btn-outline-secondary btn-sm mb-1">Ver/Editar</a>
                                    <button class="btn btn-outline-secondary btn-sm mb-1" type="button">Cambiar contraseña</button>
                                    <!-- Se inserta un boton que envie un modal para confirmar la eliminación -->
                                    <button type="button" class="btn btn-outline-danger btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#modalEliminarAdmin">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="h-100 p-3 bg-light border rounded-3">
                        <!-- Fila interna para organizar -->
                        <div class="row align-items-center h-100">
                            <!-- Imagen de la Mascota -->
                            <div class="col-sm-4 text-center mb-3 mb-sm-0">
                                <img src="/RUPE/assets/fotos/Mascota.jpg"
                                    alt="Perfil Mascota"
                                    class="img-fluid rounded-circle border shadow-sm"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                            </div>
                            <!-- Información -->
                            <div class="col-sm-8">
                                <h2>Catálogo de Mascotas</h2>
                                <p class="text-muted"></p>
                                <div class="d-grid gap-2 d-md-block">
                                    <button class="btn btn-outline-secondary btn-sm mb-1" type="button">Crear catálogo</button>
                                    <button class="btn btn-outline-secondary btn-sm mb-1" type="button">Modificar</button>
                                    <button class="btn btn-outline-secondary btn-sm mb-1" type="button">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4 mb-1 rounded-3" style="background-color: #90B6CE;">
                    <div class="container-fluid">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="h-100 p-3 bg-light border rounded-3">
                        <!-- Fila interna para organizar  -->
                        <div class="row align-items-center h-100">
                            <!-- Imagen/Icono de Reportes -->
                            <div class="col-sm-4 text-center mb-3 mb-sm-0">
                                <img src="/RUPE/assets/imagenes/perro2.jpg"
                                    alt="Icono Reporte"
                                    class="img-fluid rounded-circle border shadow-sm"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                            </div>
                            <!-- Información -->
                            <div class="col-sm-8">
                                <h2>Control de Usuarios</h2>
                                <div class="d-grid gap-2 d-md-block">
                                    <button class="btn btn-outline-secondary btn-sm mb-1" type="button">Registros</button>
                                    <button class="btn btn-outline-secondary btn-sm mb-1" type="button">Actualizar estatus</button>
                                    <button class="btn btn-outline-secondary btn-sm mb-1" type="button">Eliminar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="h-100 p-3 bg-light border rounded-3">
                        <!-- Fila interna para organizar -->
                        <div class="row align-items-center h-100">
                            <!-- Imagen/Icono del Catálogo -->
                            <div class="col-sm-4 text-center mb-3 mb-sm-0">
                                <img src="/RUPE/assets/imagenes/localizado1.webp"
                                    alt="Icono Catálogo"
                                    class="img-fluid rounded-circle border shadow-sm"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                            </div>
                            <!-- Información -->
                            <div class="col-sm-8">
                                <h2>Auditoría de Seguridad</h2>
                                <div class="d-grid gap-2 d-md-block">
                                    <button class="btn btn-outline-secondary btn-sm" type="button">Reporte de Accesos</button>
                                    <button class="btn btn-outline-secondary btn-sm" type="button">Reporte Errores del Sistema</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
    <!-- Modal para confirmación a través de la contraseña del Responsable -->
<div class="modal fade" id="modalEliminarAdmin" tabindex="-1" aria-labelledby="modalEliminarAdminLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalEliminarAdminLabel">Autorización Requerida</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form action="/RUPE/controladores/personaladmin.php?accion=eliminar" method="POST">
        <div class="modal-body">
          <p class="mb-3">¿Está seguro de que desea desactivar sus datos personales de administrador? Podrá volver a capturarlos cuando lo desee.</p>
          <div class="mb-2">
            <label for="clave_responsable" class="form-label fw-bold">Contraseña del Responsable:</label>
            <input type="password" class="form-control" id="clave_responsable" name="clave_responsable" 
                   placeholder="Ingrese la clave de autorización" required autocomplete="off">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger fw-bold">Confirmar Eliminación</button>
        </div>
      </form>
    </div>
  </div>
</div>
</main>