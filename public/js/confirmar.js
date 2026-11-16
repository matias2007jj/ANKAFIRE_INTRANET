document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            // Evita el bucle infinito: si ya fue confirmado, deja que se envíe normal
            if (form.dataset.confirmado === 'true') {
                return;
            }

            e.preventDefault();

            Swal.fire({
                title: '¿Estás seguro?',
                text: form.dataset.confirm,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then(function (resultado) {
                if (resultado.isConfirmed) {
                    form.dataset.confirmado = 'true';
                    form.submit();
                }
            });
        });
    });
});