import { Controller } from '@hotwired/stimulus';

//import $ from 'jquery';

import '../js/common/datepicker';
import { TempusDominus, extend, DateTime } from '@eonasdan/tempus-dominus';
import customDateFormat from '@eonasdan/tempus-dominus/dist/plugins/customDateFormat';

export default class extends Controller {
   static values = {
      selector: String,
      startView: String,
   }

   connect() {
      var current_locale = $('html').attr("lang");
      extend(customDateFormat);
      $(this.selectorValue).each((i,v) => {
         new TempusDominus(v,{
               display: {
                  buttons: {
                     close: true,
                  },
                  components: {
                     useTwentyfourHour: true,
                     decades: false,
                     year: true,
                     month: true,
                     date: true,
                     hours: true,
                     minutes: true,
                     seconds: false,
                  },
               },
               defaultDate: new DateTime(),
               localization: {
                  locale: current_locale,
                  dayViewHeaderFormat: { month: 'long', year: 'numeric' },
                  format: 'yyyy-MM-dd',
               },
         });
      });
   //   const options = {
   //       autoclose: true,
   //       format: "yyyy-mm-dd",
   //       language: global.locale,
   //       weekStart: 1,
   //       startView: this.startViewValue,
   //   }
   //    $(this.selectorValue).datepicker(options);
   }


}

