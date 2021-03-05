import { Component } from '@angular/core';

import { NavigationProvider } from './../../providers/navigation/navigation';

/**
 * Generated class for the BackButtonComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
  selector: 'back-button',
  templateUrl: 'back-button.html'
})
export class BackButtonComponent {

  constructor(
    public nav: NavigationProvider
  ) {

  }

  popBack()
  {
    this.nav.popBack();
  }

}
