 
 <div class="container my-2">
     <div class="card shadow-lg p-2 mx-auto" style="max-width: 900px;">
         <h1 class="text-center mb-1 fw-bold" style="color: #0F4264;">RUPE</h1>
         <h3 class="h5 text-muted mb-4 text-center">Registro de Usuarios</h3>

         <form id="formRegistro" action="index.php?c=registro&a=RegistrarUsuario" method="POST" >
             <div class="row g-5 align-items-center">
                 <div class="col-md-3 text-center d-flex align-items-center justify-content-center">
                     <img src="assets/imagenes/collageperros.jpg" alt="Collage Perros" class="img-fluid rounded shadow-sm w-100" style="object-fit: cover; max-height: 390px;">
                 </div>
                 <div class="col-md-8">
                     <div class="mb-1">
                         <label class="form-label fw-semibold">Correo Electrónico</label>
                         <input type="email" id="correo" name="correo" class="form-control" placeholder="ejemplo@correo.com" required>
                     </div>
                     <div class="row mb-2">
                         <div class="col-6">
                             <label class="form-label fw-semibold">Contraseña</label>
                             <input type="password" id="contrasena" name="contrasena" class="form-control" placeholder="********" required>
                             <small class="text-muted" style="font-size:0.72rem;">Mínimo 8 caracteres (letras y números).</small>
                         </div>
                         <div class="col-6">
                             <label class="form-label fw-semibold">Confirmar Contraseña</label>
                             <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" class="form-control" placeholder="********" required>
                         </div>
                     </div>
                     <div class="mb-4">
                         <label class="form-label fw-semibold">Rol de Usuario</label>
                         <select id="seleccionrol" name="rol" class="form-select" required>
                             <option value="" selected disabled>Seleccione un rol...</option>
                             <option value="administrador">Administrador</option>
                             <option value="usuario">Usuario</option>
                         </select>
                     </div>

                     <!-- mostrar el campo de contraseña de administrador si se selecciona el rol de administrador-->
                     <input type="hidden" name="contrasena_admin" id="contrasena_admin">


                     <!-- Contenedor para mostrar mensajes de error dinámicos -->
                     <div id="msgError" class="alert alert-danger p-2 small my-3" style="display:none;">
                     </div>
                     <div class="mb-3 d-flex justify-content-center">
                         <div class="border rounded bg-light px-3 py-2 d-flex align-items-center justify-content-between shadow-sm" style="width: 304px; height: 74px;">
                             <div class="form-check d-flex align-items-center gap-2 mb-0">
                                 <input class="form-check-input border-secondary" type="checkbox" id="checkRobotRegistro"
                                     style="width: 24px; height: 24px; cursor: pointer;"
                                     onchange="document.getElementById('tokenCaptchaRegistro').value = this.checked ? 'TOKEN_LOCAL_DE_PRUEBAS' : '';">
                                 <label class="form-check-label fw-semibold text-dark ms-1" for="checkRobotRegistro" style="cursor: pointer;">
                                     No soy un robot
                                 </label>
                             </div>
                             <div class="text-center text-muted" style="font-size: 0.65rem; line-height: 1.15;">
                                 <i class="bi bi-shield-check fs-4 text-primary d-block"></i>
                                 reCAPTCHA<br>Seguridad RUPE
                             </div>
                         </div>
                         <input type="hidden" id="tokenCaptchaRegistro" name="g-recaptcha-response" value="">
                     </div>


                     <div class="row g-3">
                         <div class="col-6">
                             <button type="submit" class="btn w-100 py-2 fw-bold shadow-sm" style="background-color: #75B0D7;">Registrar Usuario</button>
                         </div>
                         <div class="col-6">
                             <a href="index.php?c=inicio" class="btn btn-outline-secondary w-100 py-2 fw-bold shadow-sm">Regresar</a>
                         </div>
                     </div>
                 </div>
             </div>
         </form>
     </div>
 </div>
 <!-- VENTANA EMERGENTE PARA EL ADMINISTRADOR -->
 <div class="modal fade" id="modalAdmin" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content border-0 shadow">
             <div class="modal-header text-white" style="background-color: #387BA7;">
                 <h5 class="modal-title fw-bold"><i class="bi bi-shield-lock me-2"></i>Autorización Requerida</h5>
                 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body py-4">
                 <p class="mb-3 text-muted">Para registrar una cuenta con perfil de <strong>Administrador</strong>, introduzca el token o clave de autorización maestra del sistema:</p>
                 <input type="password" id="tokenAdminInput" class="form-control form-control-lg text-center" placeholder="••••••••">
                 <div id="errorModal" class="alert alert-danger p-2 small mt-3 mb-0 text-center" style="display:none;">
                     Por favor, ingrese el token de autorización.
                 </div>
             </div>
             <div class="modal-footer bg-light">
                 <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
                 <button type="button" id="btnConfirmarAdmin" class="btn btn-warning fw-bold px-4">Confirmar</button>
             </div>
         </div>
     </div>
 </div>
 <script>
    document.addEventListener("DOMContentLoaded", function() {
        const form                  = document.getElementById("formRegistro");
        const selectRol             = document.getElementById("seleccionrol");
        const contrasenaAdminOculta = document.getElementById("contrasena_admin");
        const msgError              = document.getElementById("msgError");
        const modalElement          = document.getElementById('modalAdmin');
        const btnConfirmarAdmin     = document.getElementById("btnConfirmarAdmin");
        const tokenAdminInput       = document.getElementById("tokenAdminInput");
        const checkRobot            = document.getElementById("checkRobotRegistro");
        const tokenCaptcha          = document.getElementById("tokenCaptchaRegistro");
        let bypassModal             = false;

        const modalAdmin = new bootstrap.Modal(modalElement);

        function sincronizarCaptcha() {
            if (checkRobot && tokenCaptcha) {
                tokenCaptcha.value = checkRobot.checked ? "TOKEN_LOCAL_DE_PRUEBAS" : "";
            }
        }

        form.addEventListener("submit", function(e) {
            msgError.style.display = "none";
            sincronizarCaptcha();

            const correo      = document.getElementById("correo").value.trim();
            const pass        = document.getElementById("contrasena").value;
            const confirmPass = document.getElementById("confirmar_contrasena").value;

            // 1. Validar Correo Estricto
            const regexCorreoEstricto = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!regexCorreoEstricto.test(correo)) {
                e.preventDefault();
                msgError.textContent = "Ingrese correo electrónico válido (ejemplo: usuario@dominio.com).";
                msgError.style.display = "block";
                return;
            }

            // 2. Validar Contraseña Alfanumérica (Mínimo 8 caracteres)
            const regexAlfanumerica = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;
            if (!regexAlfanumerica.test(pass)) {
                e.preventDefault();
                msgError.textContent = "La contraseña debe contener letras y números, con un mínimo de 8 caracteres.";
                msgError.style.display = "block";
                return;
            }

            // 3. Confirmar que ambas contraseñas coincidan
            if (pass !== confirmPass) {
                e.preventDefault();
                msgError.textContent = "Las contraseñas no coinciden.";
                msgError.style.display = "block";
                return;
            }

            // 4. Validar Captcha en el navegador
            if (!checkRobot || !checkRobot.checked) {
                e.preventDefault();
                msgError.textContent = "Por favor, marque la casilla 'No soy un robot'.";
                msgError.style.display = "block";
                return;
            }

            // 5. Abrir ventana emergente si es Administrador
            if (selectRol.value === "administrador" && !bypassModal) {
                e.preventDefault();
                tokenAdminInput.value = "";
                document.getElementById("errorModal").style.display = "none";
                modalAdmin.show();
            }
        });

        // Acción del botón interno del Modal
        btnConfirmarAdmin.addEventListener("click", function() {
            if (tokenAdminInput.value.trim() === "") {
                document.getElementById("errorModal").style.display = "block";
            } else {
                sincronizarCaptcha(); // Asegura que el token viaje al ejecutar form.submit()
                contrasenaAdminOculta.value = tokenAdminInput.value;
                bypassModal = true;
                modalAdmin.hide();
                form.submit();
            }
        });
    });
</script>
