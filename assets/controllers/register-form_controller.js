import { Controller } from 'stimulus';

import { useDispatch } from 'stimulus-use';

export default class extends Controller {
   static targets = [
      'form',
      'studentName', 
      'representativeName', 
      'studentDni', 
      'representativeDni', 
      'studentSurname1', 
      'representativeSurname1', 
      'studentSurname2', 
      'representativeSurname2', 
      'forMe', 
      'forMeLabel', 
      'payerRadioButton',
      'payerDni',
      'payerName',
      'payerSurname1',
      'payerSurname2',
   ];
   static values = {
      forMeLabel: String,
      notForMeLabel: String,
      user: Boolean,
   }

   connect() {
      useDispatch(this);
      if (this.forMeTarget.checked) {
         this.hideRepresentativeRadio();
      } else {
         this.showRepresentativeRadio();
      }
      if (!this.userValue) {
         if (!this.forMeTarget.checked) {
            $(this.forMeLabelTarget).find('label')[0].innerHTML=this.notForMeLabelValue;
            this.activateStudent();
         } else {
            
            $(this.forMeLabelTarget).find('label')[0].innerHTML=this.forMeLabelValue;
         }
      }
      this.addAnotherExtraField();
   }

   onSubmit(event) {
      event.preventDefault();
      this.formTarget.submit();
      this.dispatch('submit');
   }

  toggle(e) {
      let toggler = this.forMeTarget;
      if  (!toggler.checked ) {
         this.showRepresentativeRadio();
         $(this.forMeLabelTarget).find('label')[0].innerHTML=this.notForMeLabelValue;
         if (!this.userValue) {
            this.changeStudentToRepresentative();
            this.activateStudent();
         }
      } else {
         $(this.forMeLabelTarget).find('label')[0].innerHTML=this.forMeLabelValue;
         this.hideRepresentativeRadio();
         if (!this.userValue) {
            this.changeRepresentativeToStudent()
            this.activateRepresentative();
         }
      }
   }

   changeStudentToRepresentative(e) {
      this.representativeNameTarget.value = this.studentNameTarget.value;
      this.representativeSurname1Target.value = this.studentSurname1Target.value;
      this.representativeSurname2Target.value = this.studentSurname2Target.value;
      this.representativeDniTarget.value = this.studentDniTarget.value;
      this.studentNameTarget.value = '';
      this.studentSurname1Target.value = '';
      this.studentSurname2Target.value = '';
      this.studentDniTarget.value = '';
   }

   changeRepresentativeToStudent(e) {
      this.studentNameTarget.value = this.representativeNameTarget.value;
      this.studentSurname1Target.value = this.representativeSurname1Target.value;
      this.studentSurname2Target.value = this.representativeSurname2Target.value;
      this.studentDniTarget.value = this.representativeDniTarget.value;
      this.representativeNameTarget.value = '';
      this.representativeSurname1Target.value = '';
      this.representativeSurname2Target.value = '';
      this.representativeDniTarget.value = '';
   }

   activateStudent() {
      this.representativeNameTarget.setAttribute('readonly','readonly');
      this.representativeSurname1Target.setAttribute('readonly','readonly');
      this.representativeSurname2Target.setAttribute('readonly','readonly');
      this.representativeDniTarget.setAttribute('readonly','readonly');
      this.studentNameTarget.removeAttribute('readonly','');
      this.studentSurname1Target.removeAttribute('readonly','');
      this.studentSurname2Target.removeAttribute('readonly','');
      this.studentDniTarget.removeAttribute('readonly','');
   }

   activateRepresentative() {
      this.studentNameTarget.setAttribute('readonly','readonly');
      this.studentSurname1Target.setAttribute('readonly','readonly');
      this.studentSurname2Target.setAttribute('readonly','readonly');
      this.studentDniTarget.setAttribute('readonly','readonly');
      this.representativeNameTarget.removeAttribute('readonly','');
      this.representativeSurname1Target.removeAttribute('readonly','');
      this.representativeSurname2Target.removeAttribute('readonly','');
      this.representativeDniTarget.removeAttribute('readonly','');
   }

   blockPayer() {
      this.payerDniTarget.setAttribute('readonly','readonly');
      this.payerNameTarget.setAttribute('readonly','readonly');
      this.payerSurname1Target.setAttribute('readonly','readonly');
      this.payerSurname2Target.setAttribute('readonly','readonly');
   }

   unblockPayer() {
      this.payerDniTarget.removeAttribute('readonly','');
      this.payerNameTarget.removeAttribute('readonly','');
      this.payerSurname1Target.removeAttribute('readonly','');
      this.payerSurname2Target.removeAttribute('readonly','');
   }

   overwritePayerWithStudent() {
      this.payerDniTarget.value = this.studentDniTarget.value;
      this.payerNameTarget.value = this.studentNameTarget.value;
      this.payerSurname1Target.value = this.studentSurname1Target.value;
      this.payerSurname2Target.value = this.studentSurname2Target.value;
      this.blockPayer();
   }

   overwritePayerWithRepresentative() {
      this.payerDniTarget.value = this.representativeDniTarget.value;
      this.payerNameTarget.value = this.representativeNameTarget.value;
      this.payerSurname1Target.value = this.representativeSurname1Target.value;
      this.payerSurname2Target.value = this.representativeSurname2Target.value;
      this.blockPayer();
   }

   onPayerRadioButtonChange(e) {
      const value = e.target.value;
      switch ( value ) {
         case "0":
            this.overwritePayerWithStudent();
            this.blockPayer();
            break;
         case "1":
            this.overwritePayerWithRepresentative();
            this.blockPayer();
            break;
         case "2":
            this.unblockPayer();
            break;
      }
   }

   hideRepresentativeRadio() {
      if (this.hasPayerRadioButtonTarget) {
         $(this.payerRadioButtonTarget.children[1]).hide();
      }
   }

   showRepresentativeRadio() {
      if (this.hasPayerRadioButtonTarget) {
         $(this.payerRadioButtonTarget.children[1]).show();
      }
   }

   addAnotherExtraField() {
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
        var newElem = newWidget;
      //   var newElem = $(list.attr('data-widget-tags')).html(newWidget);
        // create a new list element and add it to the list
        $(newElem).appendTo(list);
      });
    }
}

