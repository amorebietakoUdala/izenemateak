import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['hiddenContent'];
    static values = {

    }

    toggle(event) {
        event.preventDefault();
        if (this.hasHiddenContentTarget) {
            if (this.hiddenContentTarget.classList.contains('visually-hidden')) {
                this.hiddenContentTarget.classList.remove('visually-hidden');
            } else {
                this.hiddenContentTarget.classList.add('visually-hidden');
            }
        } 
    }
}
