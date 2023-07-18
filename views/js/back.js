$(document).on('click', '#sincroProducts', function () {
    $("#sincroproducts-modal").modal('show');
    var datos = {
        ajax: 1,
        controller: 'AdminAccionesSincro',
        token: token,
        action: 'executeSincro'
    }

    $.ajax({
        url: 'index.php',
        data: datos,
        type: 'POST',
        dataType: 'json',
        success: function (data) {
            $("#sincroproducts-modal").modal('hide');
            $("#sincroproducts-error-modal #errorMessage").empty().append('<p>' + data.message + '</p>');
            $("#sincroproducts-error-modal").modal('show');
        }
    })
});