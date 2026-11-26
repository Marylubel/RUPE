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
                    <h5 class="display-8 fw-bold">PERFIL USUARIO</h5>
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
                                                $_SESSION['usuario_foto']
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
                                                            $_SESSION['usuario_nombre']
                                                                ?? 'Usuario'
                                                        ); ?></p>
                                <div class="d-grid gap-2 d-md-block">
                                    <a href="/RUPE/controladores/usuarios/personalusu.php?accion=editar" class="btn btn-outline-secondary btn-sm mb-1">Ver/Editar</a>
                                    <a href="/RUPE/controladores/usuarios/personalusu.php?accion=eliminar" class="btn btn-outline-danger btn-sm mb-1"
                                        onclick="return confirm('¿Estas seguro de que deseas eliminar tus datos.');">Eliminar</a>
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
                                <h2>Registro de Mascota</h2>
                                <p class="text-muted"></p>
                                <div class="d-grid gap-2 d-md-block">
                                    <a href="/RUPE/controladores/usuarios/mascotausu.php?accion=guardar" class="btn btn-outline-secondary btn-sm mb-1">Registrar</a>
                                    <a href="/RUPE/controladores/usuarios/mascotausu.php?accion=listar" class="btn btn-outline-secondary btn-sm mb-1">Ver/Modificar</a>

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
                                <h2>Reportes</h2>
                                <div class="d-grid gap-2 d-md-block">
                                    <a href="/RUPE/controladores/usuarios/reportesusu.php?accion=guardar" class="btn btn-outline-secondary btn-sm mb-1">Registrar Extravío</a>
                                    <a href="/RUPE/controladores/usuarios/reportesusu.php?accion=listar" class="btn btn-outline-secondary btn-sm mb-1">Ver/Seguimiento</a>

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
                                <h2>Seguridad y acceso</h2>
                                <div class="d-grid gap-2 d-md-block">
                                    <a href="/RUPE/controladores/usuarios/cuentasusu.php" class="btn btn-outline-secondary btn-sm mb-1">
                                        Cambiar contraseña
                                    </a>

                                    <a href="/RUPE/controladores/usuarios/cuentasusu.php?accion=eliminar"
                                        class="btn btn-outline-danger btn-sm mb-1"
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar tu cuenta? Esta acción desactivará tu acceso al sistema.');">
                                        Eliminar cuenta
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>
</main>