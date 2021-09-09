$( document ).ready(function() {
    $('form').submit(function(event) {
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: "send-crm.php",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function() {
                alert('Заявка отправлена');
            },
        });
    });
});