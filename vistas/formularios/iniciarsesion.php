<script src="https://recaptcha.net" async defer></script>

<div class="container my-4">
    <div class="card shadow-lg p-4 mx-auto" style="max-width: 900px;">
        <?php
        if(session_status() === PHP_SESSION_NONE){ session_start();}
        if (isset($_SESSION['mensaje_exito'])):?>
            <div class="alert alert-success alert-dismissible fade show p-3 small my-3 fw-bold" role="alert">
                🐾 <?php echo $_SESSION['mensaje_exito']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php
            // Borramos el mensaje de la memoria para que solo se muestre una vez
            unset($_SESSION['mensaje_exito']);
        endif;
        ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center p-2 small fw-bold mb-3">⚠️ <?php echo $error; ?></div><?php endif; ?>
        <h1 class="text-center mb-2" style="color: #0f4264;">RUPE</h1>
        <h3 class="h5 text-muted mb-2 text-center">INICIAR SESIÓN</h3>
        <form action="index.php?c=iniciarsesion&a=Validar" method="POST">
            <div class="row g-4 align-items-center">

                <div class="col-md-3 text-center d-flex align-items-center justify-content-center">
                    <img src="assets/imagenes/collageperros.jpg" alt="Collage Perros" class="img-fluid rounded shadow-sm w-100" style="object-fit: contain; max-height: 100%;">
                </div>

                <div class="col-md-9">
                    <div class="mb-2">
                        <label for="correoInput" class="form-label fw-semibold">Correo Electrónico</label>
                        <input type="email" name="correo" class="form-control" id="correoInput" value="<?php echo isset($_COOKIE['rupe_usuario']) ? htmlspecialchars($_COOKIE['rupe_usuario']) : ''; ?>" placeholder="name@example.com" required>
                    </div>

                    <div class="mb-2">
                        <label for="floatingPassword">Contraseña</label>
                        <input type="password" name="contrasena" class="form-control" id="floatingPassword" placeholder="********" required>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <div class="col-6">
                            <label class="form-check-label"><input type="checkbox" name="recordarme" class="form-check-input me-1" value="remember-me" <?php echo isset($_COOKIE['rupe_recordar']) ? 'checked' : ''; ?>>Recordarme</label>
                        </div>
                        <div class="col-6 text-end">
                            <a href="#" class="small" style="color: #0f4264;">¿Olvidaste tu contraseña?</a>
                        </div>
                    </div>

                    <!-- para prueba local, hasta que funcione correctamente el captcha-->
                    <input type="hidden" name="g-recaptcha-response" value="TOKEN_LOCAL_DE_PRUEBAS">

                    <!-- Se agrega la casilla del Captcha
                    <div class="mb-4 d-flex justify-content-center">
                        <div class="g-recaptcha" data-sitekey="6Leaaf8tAAAAABqjTD8yAs517PJ4Ji0SOZMIKLnL"></div>
                    </div>-->

                    <div class="row g-2 mt-4">
                        <div class="col-6">
                            <button type="submit" class="btn w-100 py-2 fw-bold" style="background-color: #75B0D7;">Ingresar</button>
                        </div>
                        <div class="col-6">
                            <a href="index.php?c=registro" class="btn btn-outline-secondary w-100 py-2 fw-bold">Crear cuenta</a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <a href="index.php?c=inicio" class="" style="color: #0f4264;">Regresar</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>