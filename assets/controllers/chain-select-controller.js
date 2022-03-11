import {
   Controller
} from 'stimulus';
import $ from 'jquery';

const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../public/bundles/fosjsrouting/js/router.min.js';

import {
   useDispatch
} from 'stimulus-use';

import '../js/common/select2';

export default class extends Controller {
   static targets = ['mainSelect', 'chainSelect', 'chainSelectWrapper'];
   static values = {
      locale: String,
      chooseText: String,
   }

   connect() {
      Routing.setRoutingData(routes);
      useDispatch(this, {debug: true});      
      if ( this.hasMainSelectTarget ) {
         const options = {
            language: this.localeValue,
            debug: true,
            placeholder: this.chooseTextValue,
         };
         $(this.mainSelectTarget).select2(options);
         $(this.chainSelectTarget).select2(options);
         $(this.chainSelectWrapperTarget).hide();
         $(this.mainSelectTarget).on('select2:select', function(e) {
            let event = new Event('change', { bubbles: true })
            e.currentTarget.dispatchEvent(event);
         });
      }
      // Workaround to dispatch change event on select2 input
   }

   async refreshOptions(event) {
      let mainOption = $(event.currentTarget).val();
      if ( mainOption !== "") {
          let url = app_base + Routing.generate('api_course_sessions', { id: mainOption });
          await fetch(url)
              .then( result => result.json() )
              .then( chainOptions => {
                  // If you want to leave not to choose, add this.
                  //$(this.chainSelectTarget).find('option').remove().end().append($('<option>', { value : '' }).text(this.chooseTextValue));
                  $(this.chainSelectTarget).find('option').remove().end();
                  for ( let chainOption of chainOptions ) {
                     if ( this.localeValue === 'es' ) {
                        $(this.chainSelectTarget).append($('<option>', { value : chainOption.id }).text(chainOption.descriptionEs));
                     } else {
                        $(this.chainSelectTarget).append($('<option>', { value : chainOption.id }).text(chainOption.descriptionEu));
                     }
                  }
                  $(this.chainSelectWrapperTarget).show();
              });
      }
  }

  search(event) {
     let chainOption = $(this.chainSelectTarget).val();
     let main = null;
     if ( this.hasMainSelectTarget ) {
         main = $(this.mainSelectTarget).val();
     }
     this.dispatch('search',{
        main: main,
        chainOption: chainOption
     });
   }

   clean(event) {
      if (this.hasChainSelectTarget) {
         $(this.chainSelectTarget).val('');
         $(this.chainSelectTarget).trigger('change'); 
      }
      if (this.hasMainSelectTarget) {
         $(this.mainSelectTarget).val('');
         $(this.mainSelectTarget).trigger('change');
      }
      this.search(event);
   }


}