import '../common/list';
import { createConfirmationAlert } from '../common/alert';

$(function() {
    $('#taula').bootstrapTable({
        cache: false,
        showExport: true,
        exportTypes: ['excel'],
        exportDataType: 'all',
        exportOptions: {
            fileName: "users",
            ignoreColumn: ['options']
        },
        icons: {
            export: 'fa fa-download'
        },
        showColumns: false,
        pagination: true,
        search: true,
        striped: true,
        sortStable: true,
        pageSize: 10,
        pageList: [10, 25, 50, 100],
        sortable: true,
        locale: $('html').attr('lang') + '-' + $('html').attr('lang').toUpperCase(),
    });
    var $table = $('#taula');
    $(function() {
        $('#toolbar').find('select').change(function() {
            $table.bootstrapTable('destroy').bootstrapTable({
                exportDataType: $(this).val(),
            });
        });
    });
    $(document).on('click', '.js-delete', function(e) {
        e.preventDefault();
        var url = e.currentTarget.dataset.url;
        createConfirmationAlert(url);
    });
    let $div = $('div.bootstrap-table.bootstrap4').removeClass('bootstrap4').addClass('bootstrap5');
});