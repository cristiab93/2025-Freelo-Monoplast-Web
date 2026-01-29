$(function () {
    $('#contact-form').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $nombre = $('#contact-nombre');
        const $email = $('#contact-email');
        const $mensaje = $('#contact-mensaje');
        const $submitBtn = $('#contact-submit');

        let isValid = true;

        // Reset borders
        $nombre.removeClass('error-border');
        $email.removeClass('error-border');
        $mensaje.removeClass('error-border');

        // Validation
        if (!$.trim($nombre.val())) {
            $nombre.addClass('error-border');
            isValid = false;
        }

        if (!$.trim($email.val())) {
            $email.addClass('error-border');
            isValid = false;
        } else {
            // Basic email validation
            const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailReg.test($email.val())) {
                $email.addClass('error-border');
                isValid = false;
            }
        }

        if (!$.trim($mensaje.val())) {
            $mensaje.addClass('error-border');
            isValid = false;
        }

        if (!isValid) {
            return false;
        }

        // Disable button and show loading state
        const originalBtnText = $submitBtn.text();
        $submitBtn.prop('disabled', true).text('Enviando...');

        // AJAX submit
        $.ajax({
            url: 'ajax/send-contact.php',
            type: 'POST',
            data: {
                nombre: $nombre.val(),
                email: $email.val(),
                mensaje: $mensaje.val()
            },
            dataType: 'json'
        })
            .done(function (resp) {
                if (resp && resp.success) {
                    alert(resp.message || 'Tu mensaje se envió con éxito.');
                    $form[0].reset();
                } else {
                    alert(resp.message || 'Hubo un error al enviar el mensaje.');
                }
            })
            .fail(function () {
                alert('Error de conexión. Intentá de nuevo.');
            })
            .always(function () {
                $submitBtn.prop('disabled', false).text(originalBtnText);
            });
    });

    // Remove red border on type
    $('#contact-nombre, #contact-email, #contact-mensaje').on('input', function () {
        $(this).removeClass('error-border');
    });
});
