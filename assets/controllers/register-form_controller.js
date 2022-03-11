import { Controller } from 'stimulus';

//import $ from 'jquery';

export default class extends Controller {
   static targets = ['studentName', 'representativeName', 'studentDni', 'representativeDni', 'studentSurname1', 'representativeSurname1', 'studentSurname2', 'representativeSurname2', 'forMe', 'forMeLabel'];
   static values = {
      forMeLabel: String,
      notForMeLabel: String,
   }

   connect() {
      if (!this.forMeTarget.checked) {
         $(this.forMeLabelTarget).find('label')[0].innerHTML=this.notForMeLabelValue;
         this.representativeNameTarget.setAttribute('readonly','readonly');
         this.representativeSurname1Target.setAttribute('readonly','readonly');
         this.representativeSurname2Target.setAttribute('readonly','readonly');
         this.representativeDniTarget.setAttribute('readonly','readonly');
         this.studentNameTarget.removeAttribute('readonly','');
         this.studentSurname1Target.removeAttribute('readonly','');
         this.studentSurname2Target.removeAttribute('readonly','');
         this.studentDniTarget.removeAttribute('readonly','');
      } else {
         $(this.forMeLabelTarget).find('label')[0].innerHTML=this.forMeLabelValue;
         this.studentNameTarget.setAttribute('readonly','readonly');
         this.studentSurname1Target.setAttribute('readonly','readonly');
         this.studentSurname2Target.setAttribute('readonly','readonly');
         this.studentDniTarget.setAttribute('readonly','readonly');
         this.representativeNameTarget.removeAttribute('readonly','');
         this.representativeSurname1Target.removeAttribute('readonly','');
         this.representativeSurname2Target.removeAttribute('readonly','');
         this.representativeDniTarget.removeAttribute('readonly','');
      }
   }

   changeStudentToRepresentative(e) {
      let toggler = this.forMeTarget;
      if  (!toggler.checked) {
         $(this.forMeLabelTarget).find('label')[0].innerHTML=this.notForMeLabelValue;
         this.representativeNameTarget.value = this.studentNameTarget.value;
         this.representativeSurname1Target.value = this.studentSurname1Target.value;
         this.representativeSurname2Target.value = this.studentSurname2Target.value;
         this.representativeDniTarget.value = this.studentDniTarget.value;
         this.representativeNameTarget.setAttribute('readonly','readonly');
         this.representativeSurname1Target.setAttribute('readonly','readonly');
         this.representativeSurname2Target.setAttribute('readonly','readonly');
         this.representativeDniTarget.setAttribute('readonly','readonly');
         this.studentNameTarget.value = '';
         this.studentSurname1Target.value = '';
         this.studentSurname2Target.value = '';
         this.studentDniTarget.value = '';
         this.studentNameTarget.removeAttribute('readonly');
         this.studentSurname1Target.removeAttribute('readonly');
         this.studentSurname2Target.removeAttribute('readonly');
         this.studentDniTarget.removeAttribute('readonly');
      } else {
         $(this.forMeLabelTarget).find('label')[0].innerHTML=this.forMeLabelValue;
         this.studentNameTarget.value = this.representativeNameTarget.value;
         this.studentSurname1Target.value = this.representativeSurname1Target.value;
         this.studentSurname2Target.value = this.representativeSurname2Target.value;
         this.studentDniTarget.value = this.representativeDniTarget.value;
         this.representativeNameTarget.value = '';
         this.representativeSurname1Target.value = '';
         this.representativeSurname2Target.value = '';
         this.representativeDniTarget.value = '';
         this.studentNameTarget.setAttribute('readonly','readonly');;
         this.studentSurname1Target.setAttribute('readonly','readonly');;
         this.studentSurname2Target.setAttribute('readonly','readonly');;
         this.studentDniTarget.setAttribute('readonly','readonly');;
      }
   }
}
