$(document).ready(function () {
    //The following variables are defined for the field's autocomplete.
    const operaciones = ['=', '>=', '<=', '>', '<'];
    const variables = [
        'amount',
        'personeria',
        'region',
        'tipo_doc',
        'monto',
        'comuna'
    ]
    const region = [
        'IV',
        'V',
        'VI',
        'VII',
        'VIII',
        'X',
        'XIII',
    ]

    const tipo_doc = [
        'BE',
        'BO',
        'FA',
        'FE'
    ]

    const personeria = [
        'EMPRESA',
        'PERSONA'
    ]

    //Here, the options for the "campo" (field) and "operacion" (operation) selects are set.
    $.each(variables, (i, variable) => {
        $('#campos').append($('<option>', {
            value: variable,
            text: variable
        }));
    })

    $.each(operaciones, (i, operacion) => {
        $('#operaciones').append($('<option>', {
            value: operacion,
            text: operacion
        }));
    })

    //This part is responsible for adding the fields entered in the inputs to the global filter.
    $('#btnAnhadirCampo').click(() => {
        let campo = $('#campos').val();
        let operacion = $('#operaciones').val();
        let valor = $('#valor').val();
        let filter = $('#filter').val();
        let newFilter = '';
        filter.trim();
        if (filter.length == 0) {
            newFilter = campo + ' ' + operacion + " '" + valor + "'";
            $('#filter').val(newFilter);
        } else {
            newFilter = ' and ' + campo + ' ' + operacion + " '" + valor + "'";
            $('#filter').val($('#filter').val() + newFilter);
        }
    })
    //This part cleans the global filter.
    $('#btnLimpiarFiltros').click(() => {
        $('#filter').val('');
    })

    //This code allows reusing a previously used filter. 
    //It takes the value of the filter and sets it in the global filter.
    $('.btn-usar-filtro').on('click', function (event) {
        event.preventDefault();
        let id = '#' + $(this).data('id');
        $('#filter').val($(id).text());
    });

    //This part allows displaying the suggestions in the autocomplete for the input fields.
    $('#campos').on('change', function (event) {
        event.preventDefault();
        switch ($(this).val()) {
            case 'region':
                $("#valor").autocomplete({
                    source: region,
                    minLength: 0
                });
                break;
            case 'tipo_doc':
                $("#valor").autocomplete({
                    source: tipo_doc,
                    minLength: 0
                });
                break;
            case 'personeria':
                $("#valor").autocomplete({
                    source: personeria,
                    minLength: 0
                });
                break;
            case 'comuna':
                $("#valor").autocomplete({
                    source: comuna,
                    minLength: 0
                });
                break;
            default:
                $("#valor").autocomplete({
                    source: []
                });
                break;
        }
        $("#valor").autocomplete("search", "");

    })

});