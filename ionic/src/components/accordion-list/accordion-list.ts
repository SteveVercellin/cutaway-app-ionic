import { Component, Input } from '@angular/core';

import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';

/**
 * Generated class for the AccordionListComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
  selector: 'accordion-list',
  templateUrl: 'accordion-list.html'
})
export class AccordionListComponent {

  @Input() items: any;

  constructor(
    public libFuncs: LibFunctionsProvider
  ) {

  }

  toggleContentItem(index)
  {
    for (let i = 0; i < this.items.length; i++) {
      if (i != index) {
        this.items[i]['open'] = false;
      }
    }
    this.items[index]['open'] = !this.items[index]['open'];
  }

}
