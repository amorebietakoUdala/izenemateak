import { Controller } from 'stimulus';

import { Modal } from 'bootstrap';

import '../styles/common/spinner.css';

export default class extends Controller {
   static targets = ['modal'];
   static values = {
   };

   showSpinner(event) {
      this.openModal(event);
   }

   openModal(event) {
      const modal = new Modal(this.modalTarget);
      modal.show();
  }
}