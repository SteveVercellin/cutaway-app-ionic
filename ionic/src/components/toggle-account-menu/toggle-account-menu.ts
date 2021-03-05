import { Component } from '@angular/core';
import { MenuController } from 'ionic-angular';

/**
 * Generated class for the ToggleAccountMenuComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
  selector: 'toggle-account-menu',
  templateUrl: 'toggle-account-menu.html'
})
export class ToggleAccountMenuComponent {

  constructor(
    public menu: MenuController
  ) {

  }

  openMenu()
  {
    this.menu.open('account-menu');
  }

}
