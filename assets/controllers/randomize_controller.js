import { Controller } from 'stimulus';

export default class extends Controller {
    static targets = [''];

    confirm(event) {
        event.preventDefault();
        let url = event.currentTarget.dataset.url;
        import ('sweetalert2').then(async(Swal) => {
            Swal.default.fire({
                template: '#confirmation',
            }).then((result) => {
                if ( result.isConfirmed ) {
                    document.location.href=url;
                }
            });
        });
    }
}
