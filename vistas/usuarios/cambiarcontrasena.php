
<main class="container py-5 mt-3">
    <section class="pt-3">
        <div class="col-md-7 col-lg-5 mx-auto">
            <div class="p-3 mb-1 text-black rounded-3 shadow-sm" style="background-color: #75b0d7;">
                <h4 class="fw-bold mb-1">Cambiar Contraseña</h4>
                <p class="mb-0 small">Modifica tu contraseña para mayor seguridad en tu cuenta RUPE.</p>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="/RUPE/controladores/usuarios/cuentasusu.php" method="POST" onsubmit="return validarPasswords()">
                        
                        <div class="mb-3">
                            <label for="contrasena_actual" class="form-label fw-semibold">Contraseña actual</label>
                            <div class="input-group">
                                <input type="contrasena" class="form-control" id="contrasena_actual" name="contrasena_actual" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('contrasena_actual', this)" 
                                aria-label="Mostrar u ocultar contraseña"><i class="bi bi-eye"></i></button>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="mb-3">
                            <label for="contrasena_nueva" class="form-label fw-semibold">Nueva contraseña</label>
                            <div class="input-group">
                                <input type="contrasena" class="form-control" id="contrasena_nueva" name="contrasena_nueva" minlength="6" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('contrasena_nueva', this)" 
                                aria-label="Mostrar u ocultar contraseña"><i class="bi bi-eye"></i></button>
                            </div>
                            <div class="form-text small">Mínimo 6 caracteres.</div>
                        </div>

                        <div class="mb-4">
                            <label for="contrasena_confirm" class="form-label fw-semibold">Confirmar nueva contraseña</label>
                            <div class="input-group">
                                <input type="contrasena" class="form-control" id="contrasena_confirm" name="contrasena_confirm" minlength="6" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('contrasena_confirm', this)" 
                                aria-label="Mostrar u ocultar contraseña"><i class="bi bi-eye"></i></button>
                            </div>
                            <div id="msg-pass" class="form-text text-danger fw-semibold" style="display: none;">
                                Las contraseñas no coinciden.
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn fw-bold shadow-sm" style="background-color: #75B0D7;">
                                Guardar Nueva Contraseña
                            </button>
                            <?php
                            $urlPerfil = (!empty($_SESSION['id_roles']) && ($_SESSION['id_roles'] == 1 || $_SESSION['id_roles'] === 'administrador'))
                            ? '/RUPE/index.php?c=perfiladministrador&a=Inicio'
                            : '/RUPE/index.php?c=perfilusuarios&a=Inicio';
                            ?>
                            <a href="<?= $urlPerfil ?>" class="btn btn-outline-secondary fw-bold">Cancelar y Regresar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    function togglePass(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon  = btn.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }

    function validarContrasena() {
        const nueva = document.getElementById('contrasena_nueva').value;
        const confirm = document.getElementById('contrasena_confirm').value;
        const msg = document.getElementById('msg-pass');

        if (nueva !== confirm) {
            msg.style.display = 'block';
            return false;
        }
        msg.style.display = 'none';
        return true;
    }
</script>
