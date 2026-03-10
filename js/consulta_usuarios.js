const consultarBtn = document.getElementById('consultarBtn');
const usuariosList = document.getElementById('usuariosList');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const limit = 10;
let offset = 0;

// Consultar al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    consultaUsuarios();
});

consultarBtn.addEventListener('click', () => {
    offset = 0;
    consultaUsuarios();
});

prevBtn.addEventListener('click', () => {
    if (offset > 0) {
        offset -= limit;
        consultaUsuarios();
    }
});

nextBtn.addEventListener('click', () => {
    offset += limit;
    consultaUsuarios();
});

// Buscar con Enter
document.getElementById('buscarUsuario').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        offset = 0;
        consultaUsuarios();
    }
});

function consultaUsuarios() {
    document.getElementById("img_cargando").style.visibility = "visible";
    const buscar = document.getElementById("buscarUsuario").value;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `php/consulta_usuarios.php?limit=${limit}&offset=${offset}&buscar=${encodeURIComponent(buscar)}`, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                const usuarios = response.data;
                if (usuarios.length === 0 && offset > 0) {
                    offset = 0;
                    consultaUsuarios();
                    return;
                }
                let output = '';
                let startIndex = offset + 1;
                usuarios.forEach((usuario, index) => {
                    const rolClass = usuario.rol === 'admin' ? 'rol-admin' : 'rol-usuario';
                    const rolLabel = usuario.rol === 'admin' ? 'Admin' : 'Usuario';
                    const isTargetAdmin = usuario.rol === 'admin';

                    output += `
                        <tr>
                            <td>${startIndex + index}</td>
                            <td>${usuario.user}</td>
                            <td>${usuario.name}</td>
                            <td>${usuario.apellidop}</td>
                            <td>${usuario.apellidom}</td>
                            <td><span class="rol-badge ${rolClass}">${rolLabel}</span></td>
                            <td align="center">
                                <button onclick="editar('${usuario.user}');" 
                                ${(!puedeEditar) ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}
                                style="
                                background-color: #e5e3e5; 
                                color: #333; 
                                border: none; 
                                border-radius: 5px; 
                                padding: 8px 16px; 
                                font-size: 12px; 
                                cursor: ${(puedeEditar) ? 'pointer' : 'not-allowed'}; 
                                transition: background-color 0.3s, color 0.3s;">
                                Editar
                                </button>
                            </td>
                            <td align="center">
                                <button onclick="eliminar('${usuario.user}');" 
                                ${(!puedeEliminar || isTargetAdmin) ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''}
                                style="
                                background-color: #d2d2d2; 
                                color: #333; 
                                border: none; 
                                border-radius: 5px; 
                                padding: 8px 16px; 
                                font-size: 12px; 
                                cursor: ${(puedeEliminar && !isTargetAdmin) ? 'pointer' : 'not-allowed'}; 
                                transition: background-color 0.3s, color 0.3s;">
                                Eliminar
                                </button>
                            </td>
                            <td align="center">
                                <label class="switch">
                                    <input type="checkbox" ${usuario.estatus == 1 ? 'checked' : ''} 
                                    ${(!puedeEditar || isTargetAdmin) ? 'disabled' : ''}
                                    onclick="toggleStatus('${usuario.user}', this)">
                                    <span class="slider" style="${(!puedeEditar || isTargetAdmin) ? 'opacity: 0.5; cursor: not-allowed;' : ''}"></span>
                                </label>
                            </td>
                        </tr>
                    `;
                });

                usuariosList.innerHTML = output;
                document.getElementById("img_cargando").style.visibility = "hidden";
                prevBtn.disabled = offset <= 0;
                nextBtn.disabled = offset + limit >= response.total;
            } else {
                document.getElementById("img_cargando").style.visibility = "hidden";
                usuariosList.innerHTML = '<tr><td colspan="9">No se encontraron usuarios.</td></tr>';
                prevBtn.disabled = true;
                nextBtn.disabled = true;
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error en el servidor',
                text: 'Hubo un problema al obtener la lista de usuarios.',
                confirmButtonColor: '#d63384'
            });
        }
    };
    xhr.send();
}

function editar(user) {
    var url = "./usuarios.php";
    url += "?user=" + encodeURIComponent(user);
    var nombreVentana = "ventanaEditarUsuario";
    var opciones = "width=800,height=700,scrollbars=yes,resizable=yes";
    window.open(url, nombreVentana, opciones);
}

function eliminar(user) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¡No podrás revertir esta acción! Se eliminará el usuario y todos sus permisos.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d63384',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'php/elimina_usuario.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Se eliminó correctamente el usuario!',
                            confirmButtonColor: '#d63384'
                        }).then(() => {
                            offset = 0;
                            consultaUsuarios();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
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
            xhr.send(`id=${encodeURIComponent(user)}`);
        }
    });
}

function toggleStatus(user, checkbox) {
    const estatus = checkbox.checked ? 1 : 0;

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'php/actualiza_status_usuario.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (!response.success) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'No se pudo actualizar el estatus.',
                    confirmButtonColor: '#d63384'
                });
                checkbox.checked = !checkbox.checked;
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error en el servidor',
                text: 'Hubo un problema al actualizar el estatus.',
                confirmButtonColor: '#d63384'
            });
            checkbox.checked = !checkbox.checked;
        }
    };
    xhr.send(`user=${encodeURIComponent(user)}&estatus=${estatus}`);
}
