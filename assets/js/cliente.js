$(document).ready(function () {
    // 1. Cargar clientes al iniciar la página
    cargarClientes();

    // 2. Interceptar el envío del formulario
    $('#formCliente').on('submit', function (e) {

        let formData = $(this).serialize();
        formData += '&action=guardar';

        $.ajax({
            url: '../controllers/ClienteController.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#modalCliente').modal('hide');
                    mostrarAlerta('success', response.message);
                    cargarClientes();
                } else {
                    mostrarAlerta('danger', response.message);
                }
            },
            error: function () {
                mostrarAlerta('danger', 'Ocurrió un error en el servidor.');
            }
        });
    });
});

function cargarClientes() {
    $.post('../controllers/ClienteController.php', { action: 'listar' }, function (response) {
        let html = '';
        if (response.status === 'success') {
            response.data.forEach(cliente => {
                html += `
                    <tr>
                        <td>${cliente.id_cliente}</td>
                        <td>${cliente.nombre}</td>
                        <td>${cliente.apellido}</td>
                        <td>${cliente.email}</td>
                        <td>${cliente.telefono || 'N/A'}</td>
                        <td>${cliente.fecha_registro}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="editarCliente(${cliente.id_cliente}, '${cliente.nombre}', '${cliente.apellido}', '${cliente.email}', '${cliente.telefono}')">Editar</button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarCliente(${cliente.id_cliente})">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
            $('#tabla-clientes').html(html);
        }
    }, 'json');
}

function abrirModal() {
    $('#formCliente')[0].reset();
    $('#id_cliente').val('');
    $('#modalTitulo').text('Nuevo Cliente');
    $('#modalCliente').modal('show');
}

function editarCliente(id, nombre, apellido, email, telefono) {
    $('#id_cliente').val(id);
    $('#nombre').val(nombre);
    $('#apellido').val(apellido);
    $('#email').val(email);
    $('#telefono').val(telefono !== 'null' ? telefono : '');

    $('#modalTitulo').text('Editar Cliente');
    $('#modalCliente').modal('show');
}

function eliminarCliente(id) {
    if (confirm('¿Estás seguro de eliminar este cliente?')) {
        $.post('../controllers/ClienteController.php', { action: 'eliminar', id_cliente: id }, function (response) {
            if (response.status === 'success') {
                mostrarAlerta('success', response.message);
                cargarClientes();
            } else {
                mostrarAlerta('danger', response.message);
            }
        }, 'json');
    }
}

function mostrarAlerta(tipo, mensaje) {
    const html = `<div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>`;
    $('#alerta-sistema').html(html);
}