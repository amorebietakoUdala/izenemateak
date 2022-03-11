import { Controller } from 'stimulus';

import $ from 'jquery';

export default class extends Controller {
    static targets = ['form','startDate','endDate'];
    static values = {

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
}
