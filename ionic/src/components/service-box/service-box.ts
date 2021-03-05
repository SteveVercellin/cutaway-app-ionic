import { Component, Input } from '@angular/core';
import { TranslateService } from '@ngx-translate/core';

/**
 * Generated class for the ServiceBoxComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
  selector: 'service-box',
  templateUrl: 'service-box.html'
})
export class ServiceBoxComponent {

  @Input() service: any;

  constructor(
    translate: TranslateService
  ) {

  }

}
