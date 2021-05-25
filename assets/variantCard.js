$(document).ready(function () {
    $(".variant-card").on("change", ".selectVariantTaille", function (event) {
        stock = parseInt($('option:selected', this).data('stock'));
        if (isNaN(stock) || stock <= 0) {
            $('.add2cart', $(this).parent()).attr('disabled', 'disabled');
            $('.stock-info', $(this).parents('.variant-card')).html("Sélectionner une taille pour voir le stock");
        } else {
            $('.add2cart', $(this).parent()).removeAttr('disabled');
            $('.cartqte', $(this).parent()).attr('max', stock).val(1);
            $('.stock-info', $(this).parents('.variant-card')).html(stock + " unité(s) en stock");
        }
    });
    $(".variant-card").on("click", ".add2cart", function (event) {
        $('.modal-body', '#modalInfo').html("Ajout en cours");
        $('#modalInfo').modal('show');
        $.ajax({
            url: '/cart/add',
            type: 'POST',
            dataType: 'json',
            async: true,
            data: {
                "variant": $(this).data('vid'),
                "size": $('.selectVariantTaille', $(this).parent()).val(),
                "qte": $('.cartqte', $(this).parent()).val()
            },


            success: function (data, status) {
                if (data.addOk) {
                    $('.modal-body', '#modalInfo').html("Le produit a été ajouté");
                    $('#cartQte').html(data.cartQte);
                } else {
                    switch (data.info) {
                        case 1:
                            txt = "Il n'y a pas assez d'article en stock";
                            break;
                        default:
                            txt = "Impossible d'ajouter cet article";
                    }
                    $('.modal-body', '#modalInfo').html(txt);
                }

            },
            error: function (xhr, textStatus, errorThrown) {
                $('.modal-body', '#modalInfo').html("Impossible d'ajouter ce produit pour le moment");

            },
        });
    });
});