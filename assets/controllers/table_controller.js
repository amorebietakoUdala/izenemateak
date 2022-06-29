import { Controller } from 'stimulus';

import '../js/common/list';

export default class extends Controller {
   static targets = [];
   static values = {
       exportName: String,
       pageSize: Number,
   }

    connect() {
        $(this.element).bootstrapTable({
            cache: false,
            showExport: true,
            exportTypes: ['excel'],
            exportDataType: 'all',
            exportOptions: {
                fileName: this.exportNameValue,
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
            pageSize: this.hasPageSizeValue ? this.pageSizeValue : 10,
            pageList: [10, 25, 50, 100],
            sortable: true,
            locale: $('html').attr('lang') + '-' + $('html').attr('lang').toUpperCase(),
        });
        var $table = $(this.element);
        $(function() {
            $('#toolbar').find('select').change(function() {
                $table.bootstrapTable('destroy').bootstrapTable({
                    exportDataType: $(this).val(),
                });
            });
        });
        let $div = $('div.bootstrap-table.bootstrap4').removeClass('bootstrap4').addClass('bootstrap5');
    }
  
}
