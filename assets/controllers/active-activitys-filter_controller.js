import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = ['form'];

    onChange(event) {
        this.formTarget.submit();
    }
}
