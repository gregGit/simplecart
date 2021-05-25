/**
 * Route car-content : Affichage du panier
 *
 * Le js permet de gérer les appels ajax  :
 * - lors de la modification des quantités (/cart/setqty)
 * - lors de la suppression d'un élément (/car/remove
 */
$(document).ready(function () {
    /**
     * Click sur un bouton de suppression
     * l'appel ajax est fait, et les quantité/montant du panier sont actualiées
     * En cas d'erreur ou s'il ne reste plus aucun article la page est réactualisée
     */
    $('.cart-content-body').on('click', '.btn-remove', function (event) {
        parentRow = $(this).parents('.cart-content-body');
        $.ajax({
            url: '/cart/remove',
            type: 'POST',
            dataType: 'json',
            async: true,
            data: {
                "variant": $(this).data('vid'),
                "size": $(this).data('size'),
            },
            success: function (data, status) {
                if (data.addOk) {
                    $('#cartQte').html(data.cartQte);
                    $('#cardTotal').html(data.cartAmount);
                    parentRow.remove();
                    if ($('.cart-content-body').length < 1) {
                        document.location.reload();
                    }
                } else {
                    document.location.reload();
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                document.location.reload();
            },
        });
    });

    /**
     * mise à jours de la quantité lorsque l'on quitte le input qte.
     * En cas d'erreur affichage d'un message
     * Sans erreur mise à jours de la ligne, de la quantité totale, du montant total.
     */
    $('.cart-content-body').on('blur', 'input[name="qte"]', function (event) {
        newQte = parseInt($(this).val());
        initialQte = parseInt($(this).data('initial-value'));
        stock = parseInt($(this).data('stock'));
        inputQte = $(this);
        if (newQte == initialQte) {
            return true;
        }
        if (newQte > stock) {
            $(this).val(initialQte);
            return true;
        }
        inputQte.attr('disabled', 'disabled');
        $.ajax({
            url: '/cart/setqty',
            type: 'POST',
            dataType: 'json',
            async: true,
            data: {
                "variant": $(this).data('vid'),
                "size": $(this).data('size'),
                "qte": $(this).val()
            },
            success: function (data, status) {
                if (data.addOk) {
                    $('#cartQte').html(data.cartQte);
                    $('#cardTotal').html(data.cartAmount);
                    $('.amount', inputQte.parents('.cart-content-body')).html(data.itemAmount);
                } else {
                    switch (data.info) {
                        case 1:
                            txt = "Il n'y a pas assez d'article en stock";
                            break;
                        case 2:
                            txt = "Cet article n'est plus dans le panier";
                            break;
                        default:
                            txt = "Impossible d'ajouter cet article";
                    }
                    inputQte.val(inputQte.data('initial-value'));
                    alert(txt);
                }
                inputQte.removeAttr('disabled');
            },
            error: function (xhr, textStatus, errorThrown) {
                inputQte.val(inputQte.data('initial-value'));
                inputQte.removeAttr('disabled');
            },
        });

    });
});