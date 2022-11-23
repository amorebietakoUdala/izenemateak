import { Controller } from '@hotwired/stimulus';

//import $ from 'jquery';
import '../js/common/select2';
import '../js/common/autocomplete.js';

export default class extends Controller {
    static targets = ['form','startDate','endDate','accountingConceptSelect'];
    static values = {
      locale: String,
      autocompleteServiceUrl: String,
    }

    connect() {
      if ( this.accountingConceptSelectTarget ) {
        const options = {
          language: this.localeValue,
        };
        $(this.accountingConceptSelectTarget).select2(options);
      }
      this.addAnotherCollectionListener();
    }

    deleteExtraField(event) {
      $(event.currentTarget.closest('li')).remove();
    }    

    onSubmit(event) {
      event.preventDefault();
      let startDate = new Date(this.startDateTarget.value);
      let endDate = new Date(this.endDateTarget.value);
      if (startDate > endDate) {
        import ('sweetalert2').then(async(Swal) => {
          Swal.default.fire({
              template: '#dateError',
          });
        });
      } else {
        this.formTarget.submit();
      }
    }

    addAutocompleteTo(selector, locale = 'es') {
      const url = this.autocompleteServiceUrlValue;
      const options = {
        minChars: 2,
        serviceUrl: url,
        triggerSelectOnValidInput: true,
        paramName: "name",
        params: {
          'locale': locale,
        },
        transformResult: function(response) {
          var json_data = JSON.parse(response);
          return {
            suggestions: $.map(json_data, function(dataItem) {
              if (locale == 'es') {
                return { value: dataItem.name, data: dataItem.id, otherValue: dataItem.nameEu };
              } else {
                return { value: dataItem.nameEu, data: dataItem.id, otherValue: dataItem.name };
              }
            })
          };
        },
        onSelect: function (suggestion) {
          console.log('Onselect');
          console.log(suggestion, locale);
          if (locale == 'es') {
            var input_idEu = $(this).attr('id').replace('name', 'nameEu');
            console.log(input_idEu);
            $(document).find('#'+input_idEu).val(suggestion.otherValue);
          } else {
            var input_idEs = $(this).attr('id').replace('nameEu', 'name');
            console.log(input_idEs);
            $(document).find('#'+input_idEs).val(suggestion.otherValue);
          }
        }
      };
      $(selector).autocomplete(options);
    }

    addAnotherCollectionListener() {
      const handler = this;
      $('.add-another-collection-widget').click(function (e) {
        var elem = $(this).attr('data-list-selector');
        var list = $(elem);
        // Try to find the counter of the list or use the length of the list
        var counter = list.data('widget-counter') || list.children().length;

        // grab the prototype template
        var newWidget = list.attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);
        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data('widget-counter', counter);

        // create a new list element and add it to the list
        $(newWidget).appendTo(list);
        const newWidgetId = $(newWidget).find('input')[0].id;
        handler.addAutocompleteTo("#"+newWidgetId);
        handler.addAutocompleteTo("#"+newWidgetId+'Eu','eu');
      });
    }
}
