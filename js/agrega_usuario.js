document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('usuarioForm');
    const formTitle = document.getElementById('formTitle');
    const userInput = document.getElementById('user');
    const passwordInput = document.getElementById('password');
    const passwordNote = document.getElementById('passwordNote');
    const submitBtn = document.getElementById('submitBtn');

    // Detectar modo edición por parámetro ?user= en URL
    const urlParams = new URLSearchParams(window.location.search);
    const editUser = urlParams.get('user');
    let isEditMode = false;

    if (editUser) {
        isEditMode = true;
        formTitle.textContent = 'Editar Usuario';
        submitBtn.value = 'Actualizar';
        userInput.value = editUser;
        userInput.readOnly = true;
        userInput.style.backgroundColor = '#ddd';
        passwordInput.required = false;
        passwordInput.placeholder = 'Dejar vacío para no cambiar';
        passwordNote.style.display = 'block';

        // Cargar datos del usuario
        cargarUsuario(editUser);
    }

    // Botón Seleccionar Todos
    document.getElementById('btnSelectAll').addEventListener('click', () => {
        document.querySelectorAll('.permisos-table input[type="checkbox"]').forEach(cb => {
            cb.checked = true;
        });
    });

    // Botón Quitar Todos
    document.getElementById('btnDeselectAll').addEventListener('click', () => {
        document.querySelectorAll('.permisos-table input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
        });
    });

    // Envío del formulario
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const user = userInput.value.trim();
        const password = passwordInput.value;
        const name = document.getElementById('name').value.trim();
        const apellidop = document.getElementById('apellidop').value.trim();
        const apellidom = document.getElementById('apellidom').value.trim();
        const rol = document.getElementById('rol').value;

        if (!user || !name || !apellidop || !apellidom) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos requeridos',
                text: 'Por favor complete todos los campos obligatorios.',
                confirmButtonColor: '#d63384'
            });
            return;
        }

        if (!isEditMode && !password) {
            Swal.fire({
                icon: 'warning',
                title: 'Contraseña requerida',
                text: 'Debe indicar una contraseña para el nuevo usuario.',
                confirmButtonColor: '#d63384'
            });
            return;
        }

        // Recopilar permisos
        const permisos = recopilarPermisos();

        const url = isEditMode ? 'php/actualiza_usuario.php' : 'php/agrega_usuario.php';

        const params = new URLSearchParams();
        params.append('user', user);
        params.append('password', password);
        params.append('name', name);
        params.append('apellidop', apellidop);
        params.append('apellidom', apellidom);
        params.append('rol', rol);
        params.append('permisos', JSON.stringify(permisos));

        const xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: isEditMode ? '¡Usuario actualizado!' : '¡Usuario registrado!',
                        text: isEditMode ? 'Los datos se actualizaron correctamente.' : 'El usuario fue creado exitosamente.',
                        confirmButtonColor: '#d63384'
                    }).then(() => {
                        if (!isEditMode) {
                            form.reset();
                            document.querySelectorAll('.permisos-table input[type="checkbox"]').forEach(cb => {
                                cb.checked = false;
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Ocurrió un error al procesar la solicitud.',
                        confirmButtonColor: '#d63384'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en el servidor',
                    text: 'Hubo un problema al procesar su solicitud.',
                    confirmButtonColor: '#d63384'
                });
            }
        };

        xhr.send(params.toString());
    });

    function cargarUsuario(user) {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `php/consulta_usuario_actualizar.php?user=${encodeURIComponent(user)}`, true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    const data = response.data;
                    document.getElementById('name').value = data.name || '';
                    document.getElementById('apellidop').value = data.apellidop || '';
                    document.getElementById('apellidom').value = data.apellidom || '';
                    document.getElementById('rol').value = data.rol || 'usuario';
                    document.getElementById('password').value = data.password || '';

                    // Cargar permisos en checkboxes
                    if (data.permisos && Array.isArray(data.permisos)) {
                        data.permisos.forEach(perm => {
                            const acciones = ['ver', 'crear', 'editar', 'eliminar', 'corte'];
                            const isTargetAdmin = data.rol === 'admin';

                            acciones.forEach(accion => {
                                const cb = document.querySelector(
                                    `input[data-modulo="${perm.modulo}"][data-accion="${accion}"]`
                                );
                                if (cb) {
                                    cb.checked = perm[accion];
                                    if (isTargetAdmin) cb.disabled = true;
                                }
                            });
                        });
                    }

                    // Si es admin, bloquear parcialmente el formulario
                    if (data.rol === 'admin') {
                        document.getElementById('name').readOnly = false;
                        document.getElementById('apellidop').readOnly = false;
                        document.getElementById('apellidom').readOnly = false;
                        document.getElementById('rol').disabled = true;
                        document.getElementById('password').disabled = false;
                        document.getElementById('submitBtn').disabled = false;
                        document.getElementById('submitBtn').style.opacity = '1';
                        document.getElementById('submitBtn').style.cursor = 'pointer';
                        document.getElementById('btnSelectAll').disabled = true;
                        document.getElementById('btnDeselectAll').disabled = true;

                        Swal.fire({
                            icon: 'info',
                            title: 'Usuario Administrador',
                            text: 'Puedes editar los datos generales y contraseña, pero sus permisos y rol son intocables.',
                            confirmButtonColor: '#d63384'
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudo cargar los datos del usuario.',
                        confirmButtonColor: '#d63384'
                    });
                }
            }
        };
        xhr.send();
    }

    function recopilarPermisos() {
        const modulos = ['citas', 'clientas', 'colaboradoras', 'servicios', 'municipios', 'gastos', 'inventario', 'usuarios'];
        const acciones = ['ver', 'crear', 'editar', 'eliminar', 'corte'];
        const permisos = [];

        modulos.forEach(modulo => {
            const perm = { modulo: modulo };
            acciones.forEach(accion => {
                const cb = document.querySelector(`input[data-modulo="${modulo}"][data-accion="${accion}"]`);
                perm[accion] = cb ? cb.checked : false;
            });
            permisos.push(perm);
        });

        return permisos;
    }
});
