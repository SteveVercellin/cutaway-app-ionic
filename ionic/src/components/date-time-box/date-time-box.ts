import { Component, Input } from '@angular/core';
import { TranslateService } from '@ngx-translate/core';

import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';

/**
 * Generated class for the DateTimeBoxComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
  selector: 'date-time-box',
  templateUrl: 'date-time-box.html'
})
export class DateTimeBoxComponent {

  @Input() data: any;

  constructor(
    public libFunc: LibFunctionsProvider,
    translate: TranslateService
  ) {

  }

}
