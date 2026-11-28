<footer class="text-muted py-4 border-top bg-light mt-auto">
  <div class="container">
    <p class="float-end mb-1">
      <a href="#" class="text-decoration-none" style="color: #0f4264;">⬆️ Volver arriba</a>
    </p>
    <p class="mb-1 fw-bold">RUPE &copy; Registro Único de Perros Extraviados</p>
    <p class="mb-0 text-secondary">Ciudad de México</p>
  </div>
</footer>

<!-- Script de Bootstrap permite la apertura de menús desplegables -->
<script src="/RUPE/assets/js/bootstrap.bundle.min.js"></script>

</body>

</html>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Selecciona todos los inputs de texto y textareas (excluyendo correos, contraseñas, CURP y códigos)
        const camposTexto = document.querySelectorAll(
            'input[type="text"]:not([name="curp"]):not([name="numint"]):not([name="numext"]):not([name="codigo_postal"]), textarea'
        );

        camposTexto.forEach(campo => {
            campo.addEventListener("blur", function () {
                if (this.value.trim() !== "") {
                    // Convierte cada palabra a Inicial Mayúscula y el resto en minúscula (respetando acentos y ñ)
                    this.value = this.value
                        .trim()
                        .toLocaleLowerCase('es-MX')
                        .replace(/(^|\s)([a-záéíóúüñ])/g, function (match, espacio, letra) {
                            return espacio + letra.toLocaleUpperCase('es-MX');
                        });
                }
            });
        });
    });
</script>
