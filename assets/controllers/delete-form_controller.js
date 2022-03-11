import { Controller } from 'stimulus';

import $ from 'jquery';
import Translator from 'bazinga-translator';
const translations = require('../../public/translations/' + Translator.locale + '.json');

import Swal from 'sweetalert2';

export default class extends Controller {
    static targets = ['deleteForm'];
    static values = {};

    onSubmit(e) {
      let form = this.deleteFormTarget;
      Translator.fromJSON(translations);
      Swal.fire({
          title: Translator.trans('messages.confirmacion'),
          html: Translator.trans('messages.confirmationDetail'),
          type: 'warning',
          showCancelButton: true,
          cancelButtonText: Translator.trans('messages.no'),
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: Translator.trans('messages.si'),
      }).then(function(result) {
        form.submit();
      });
      }
}
