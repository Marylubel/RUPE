<title>Perfil Administrador</title>

<link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/checkout/">



<!-- Bootstrap core CSS -->
<link href="/RUPE/assets/css/bootstrap.min.css" rel="stylesheet">

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

<!-- Estilos de la plantilla -->
<link href="/RUPE/assets/css/form-validation.css" rel="stylesheet">

<body class="bg-light">

    <div class="container">
        <main>
            <form action="/RUPE/controladores/usuarios/mascotausu.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <input type="hidden" name="id_mascota" value="<?= htmlspecialchars($mascota['id_mascota']  ?? ''); ?>">

                <section class="py-5">
                    <div class="col-md-8 mx-auto bg-white p-3 rounded border shadow-sm">

                        <!-- Espacio Foto e información de la Mascota  -->
                        <div class="row align-items-center justify-content-center g-3">
                            <div class="col-auto text-center">
                                <div class="mb-2 position-relative d-inline-block">
                                    <img id="img-visualizacion" class="d-block mx-auto rounded-circle border shadow-sm"
                                        src="<?= htmlspecialchars($mascota['foto'] ?? '/RUPE/assets/fotos/Mascota.jpg'); ?>"
                                        alt="Foto de mascota"
                                        width="120" height="120" style="object-fit: cover;">
                                </div>
                            </div>

                            <div class="col-md-7">
                                <h4 class="mb-2 text-md-start text-center">Registro de Mascota</h4>
                                <p class="text-muted fs-6 mb-2 text-md-start text-center">Ingrese la información de su Mascota y suba una fotografía reciente para su identificación.</p>

                                <!-- Botón para seleccionar foto -->
                                <div>
                                    <label for="foto" class="form-label fw-bold mb-1 small">Selecciona una foto de su Mascota</label>
                                    <input class="form-control form-control-sm" type="file" id="foto" name="foto"
                                        accept="image/*" onchange="visualizarFotoMascota(this)">
                                    <div class="form-text" style="font-size: 0.75rem;">Se permiten formatos: JPG, PNG. Máximo 2MB.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Encabezado datos de la mascota -->
                <div class="col-12 mx-auto">
                    <h4 class="mb-2">Datos de su Mascota</h4>

                    <!-- Primera línea del formulario -->
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label for="nombre" class="form-label">Nombre de su Mascota</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Pequitas" value="<?= htmlspecialchars($mascota['nombre'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="id_especie" class="form-label fw-semibold">Especie</label>
                            <select id="id_especie" name="id_especie" class="form-select" onchange="cargarRazasPorEspecie()" required>
                                <option value="" <?= empty($mascota['id_especie']) ? 'selected' : '' ?> disabled>Seleccione la especie</option>
                                <?php if (!empty($listaEspecies)): ?>
                                    <?php foreach ($listaEspecies as $esp): ?>
                                        <option value="<?= $esp['id_especie'] ?>" <?= (isset($mascota['id_especie']) && $mascota['id_especie'] == $esp['id_especie']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($esp['nom_especie']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="id_raza" class="form-label fw-semibold">Razas</label>
                            <select id="id_raza" name="id_raza" class="form-select" data-raza="<?= htmlspecialchars($mascota['id_raza'] ?? '') ?>" disabled onchange="mostrarFotoCatalogo()">
                                <option value="" selected disabled>Seleccione raza...</option>
                            </select>
                        </div>
                        <div class="col-md-2 text-center">
                            <img id="foto_referencia_raza" src="/RUPE/assets/fotos/Mascota.jpg"
                                alt="Referencia" class="img-thumbnail shadow-sm" style="height: 65px; width: 65px; object-fit: cover;">
                            <div class="form-text" style="font-size: 0.7rem;">Foto Muestra</div>
                        </div>
                    </div>
                    <!-- Segunda línea del formulario -->
                    <div class="row g-5 my-1">
                        <div class="col-md-2">
                            <label for="id_sexos" class="form-label fw-semibold">Sexo</label>
                            <select id="id_sexos" name="id_sexos" class="form-select">
                                <option value="" <?= empty($mascota['id_sexos']) ? 'selected' : '' ?> disabled>Seleccione...</option>
                                <?php if (!empty($listaSexos)): ?>
                                    <?php foreach ($listaSexos as $s): ?>
                                        <option value="<?= $s['id_sexo_masc'] ?>" <?= (isset($mascota['id_sexos']) && $mascota['id_sexos'] == $s['id_sexo_masc']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($s['sexos']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="edad" class="form-label">Edad</label>
                            <input type="number" class="form-control" id="edad" name="edad" min="0" max="100" step="1"
                                placeholder="En años" value="<?= htmlspecialchars($mascota['edad'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="color" class="form-label fw-semibold">Color</label>
                            <?php
                            $colores = [
                                'negro',
                                'blanco',
                                'café / chocolate',
                                'miel / dorado / beige',
                                'gris / plata',
                                'rojizo / canela',
                                'negro con blanco',
                                'café con blanco',
                                'miel con blanco',
                                'negro con café (fuego)',
                                'atigrado',
                                'tricolor',
                                'manchado / pinto'
                            ];
                            $colorGuardado = $mascota['color'] ?? '';
                            ?>
                            <select id="color" name="color" class="form-select">
                                <option value="" <?= empty($colorGuardado) ? 'selected' : '' ?> disabled>Seleccione color</option>
                                <?php foreach ($colores as $c): ?>
                                    <option value="<?= $c ?>" <?= ($colorGuardado === $c) ? 'selected' : '' ?>><?= $c ?></option>
                                <?php endforeach; ?>                                
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="tamano" class="form-label fw-semibold">Tamaño</label>
                            <?php
                            $tamanos = ['grande', 'mediano', 'pequeño'];
                            $tamanoGuardado = $mascota['tamano'] ?? '';
                            ?>
                            <select id="tamano" name="tamano" class="form-select">
                                <option value="" <?= empty($tamanoGuardado) ? 'selected' : '' ?> disabled>Seleccione...</option>
                                <?php foreach ($tamanos as $t): ?>
                                    <option value="<?= $t ?>" <?= ($tamanoGuardado === $t) ? 'selected' : '' ?>><?= $t ?></option>
                                <?php endforeach; ?>                                
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="peso_aprox" class="form-label">Peso aprox. (kg)</label>
                            <input type="number" step="0.1" min="0.1" max="120" class="form-control" id="peso_aprox" name="peso_aprox"
                                placeholder="kg" value="<?= htmlspecialchars($mascota['peso_aprox'] ?? ''); ?>">
                        </div>

                    </div>
                    <!-- Tercera línea del formulario -->
                    <div class="row g-3 my-1">
                        <div class="col-md-12">
                            <label for="sena_particular" class="form-label fw-semibold">Señas particulares de su Mascota</label>
                            <textarea class="form-control" id="sena_particular" name="sena_particular" rows="4" style="overflow:hidden; resize:vertical;" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                                placeholder="Describa a su mascota: manchas, cicatrices, collar que usa, etc. Todo detalle que ayude a identificarlo"><?= htmlspecialchars($mascota['sena_particular'] ?? ''); ?></textarea>
                            <div class="form-text"></div>
                        </div>

                        <div class="row g-2 my-1">
                            <div class="col-md-6">
                                <button id="btnGuardar" class="w-100 btn btn-lg fw-bold shadow-sm" style="background-color: #75b0d7;" type="submit">Guardar</button>
                            </div>
                            <div class="col-md-6">
                                <a href="/RUPE/controladores/usuarios/mascotausu.php?accion=listar" class="btn btn-lg w-100 py-2 fw-bold shadow-sm" style="background-color: #75b0d7;">Ver Registros</a>
                            </div>
                        </div>
                    </div>
            </form>
        </main>
    </div>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/form-validation.js"></script>

</body>


<!-- JAVASCRIPT -->
<script>
    const urlControladorMascota = '/RUPE/controladores/usuarios/mascotausu.php';

    //Visualizar foto de la mascota
    function visualizarFotoMascota(input) {
        if (input.files && input.files) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('img-visualizacion-mascota').src = e.target.result;
            };
            reader.readAsDataURL(input.files);
        }
    }
    //para visualizar razas despues de seleccionar especie
    function cargarRazasPorEspecie() {
        const especieId = document.getElementById('id_especie').value;
        const selectRaza = document.getElementById('id_raza');
        const razaGuardada = selectRaza.dataset.raza || '';

        selectRaza.innerHTML = '<option value="" selected disabled>Cargando razas...</option>';
        selectRaza.disabled = true;

        if (!especieId) return;

        fetch(`${urlControladorMascota}?action=getRazas&id_especie=${especieId}`)
            .then(res => res.json())
            .then(razas => {
                selectRaza.innerHTML = '<option value="" selected disabled>Seleccione una raza...</option>';
                if (Array.isArray(razas) && razas.length > 0) {
                    razas.forEach(r => {
                        const option = document.createElement('option');
                        option.value = r.id_raza;
                        option.textContent = r.nom_raza;
                        option.dataset.foto = r.foto || '/RUPE/assets/fotos/Mascotas.jpg';

                        // Selecciona automaticamente la raza guardada al editar
                        if (razaGuardada && String(r.id_raza) === String(razaGuardada)) {
                            option.selected = true;
                        }
                        selectRaza.appendChild(option);
                    });
                    selectRaza.disabled = false;
                    mostrarFotoCatalogo();
                } else {
                    selectRaza.innerHTML = '<option value="" selected disabled>No hay razas registradas</option>';
                }
            })
            .catch(err => console.error('Error al cargar razas', err));
    }

    //Mostrar foto del catalogo
    function mostrarFotoCatalogo() {
        const selectRaza = document.getElementById('id_raza');
        const opcion = selectRaza.options[selectRaza.selectedIndex];
        if (opcion && opcion.dataset.foto) {
            document.getElementById('foto_referencia_raza').src = opcion.dataset.foto;
        }
    }
    //Para que no haya signos negativos en el peso
    const inputPeso = document.getElementById('peso_aprox');
    if (inputPeso) {
        inputPeso.addEventListener('keydown', function(e) {
            if (['-', '+', 'e', 'E'].includes(e.key)) e.preventDefault();
        });
    }
    
    window.addEventListener('load', function() {
        const selectEspecie = document.getElementById('id_especie');
        if (selectEspecie && selectEspecie.value !== '') {
            cargarRazasPorEspecie();
        }
    });

    //Asegurar que id_raza se guarde
    document.querySelector('form').addEventListener('submit', function() {
        document.getElementById('id_raza').disabled = false;
    });
</script>