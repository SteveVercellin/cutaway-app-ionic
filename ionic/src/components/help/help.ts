import { Component } from '@angular/core';

import { TranslateService } from '@ngx-translate/core';

import { NavigationProvider } from './../../providers/navigation/navigation';

/**
 * Generated class for the HelpComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
  selector: 'help',
  templateUrl: 'help.html'
})
export class HelpComponent {

  constructor(
    public nav: NavigationProvider,
    translate: TranslateService
  ) {

  }

  goToSupportPage()
  {
    let dataPassed = {
      'open_page_type': 'push'
    };
    this.nav.pushToPage('SupportPage', dataPassed);
  }

}
