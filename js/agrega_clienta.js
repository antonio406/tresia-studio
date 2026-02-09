document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('clientaForm');
    const messageDiv = document.getElementById('message');

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const dia = document.getElementById('dia').value;
        const hora = document.getElementById('hora').value;
        const colaboradora = document.getElementById('colaboradora').value;
        const clienta = document.getElementById('clienta').value;
        const servicio = document.getElementById('servicio').value;
        const descuento = document.getElementById('descuento').value;
        const trans = document.getElementById('trans').value;
        const efectivo = document.getElementById('efectivo').value;
        const propina = document.getElementById('propina').value;
        const total = document.getElementById('total').value;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'php/agrega_clienta.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Registro exitoso!',
                        text: 'El registro de la clienta ha sido exitoso.',
                        confirmButtonColor: '#d63384'
                    });
                    loginForm.reset(); // Limpiar el formulario
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Nombre de usuario o contraseña inválidos.',
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

        xhr.send(`dia=${encodeURIComponent(dia)}&hora=${encodeURIComponent(hora)}
                &colaboradora=${encodeURIComponent(colaboradora)}&clienta=${encodeURIComponent(clienta)}
                &servicio=${encodeURIComponent(servicio)}&descuento=${encodeURIComponent(descuento)}
                &trans=${encodeURIComponent(trans)}&efectivo=${encodeURIComponent(efectivo)}
                &propina=${encodeURIComponent(propina)}&total=${encodeURIComponent(total)}`);
    });
    const descuento = document.getElementById('descuento');
    const trans = document.getElementById('trans');
    const efectivo = document.getElementById('efectivo');
    const propina = document.getElementById('propina');
    const total = document.getElementById('total');

    function calcularTotal() {
        const descuentoVal = parseFloat(descuento.value) || 0;
        const transVal = parseFloat(trans.value) || 0;
        const efectivoVal = parseFloat(efectivo.value) || 0;
        const propinaVal = parseFloat(propina.value) || 0;

        const sumaTotal = (transVal + efectivoVal + propinaVal) - descuentoVal;
        total.value = sumaTotal.toFixed(2); // Mantener dos decimales
    }

    // Escuchar los cambios en los campos numéricos
    descuento.addEventListener('input', calcularTotal);
    trans.addEventListener('input', calcularTotal);
    efectivo.addEventListener('input', calcularTotal);
    propina.addEventListener('input', calcularTotal);
});
