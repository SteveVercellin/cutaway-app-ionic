import { Component } from '@angular/core';
import { MenuController } from 'ionic-angular';

/**
 * Generated class for the OpenOptionsIconComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
  selector: 'open-options-icon',
  templateUrl: 'open-options-icon.html'
})
export class OpenOptionsIconComponent {

  constructor(
    public menu: MenuController
  ) {

  }

  openMenu()
  {
    this.menu.open('options-menu');
  }

}
