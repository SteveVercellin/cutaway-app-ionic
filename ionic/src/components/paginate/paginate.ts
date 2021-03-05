import { Component, Input } from '@angular/core';
import { Events } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';

import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';

/**
 * Generated class for the PaginateComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
  selector: 'paginate',
  templateUrl: 'paginate.html'
})
export class PaginateComponent {

  @Input() data: any;
  pages;

  constructor(
    public event: Events,
    public libFuncs: LibFunctionsProvider,
    translate: TranslateService
  ) {
  }

  ngOnChanges()
  {
    this.mergeWithDefaultValue();
    this.buildPages();
  }

  mergeWithDefaultValue()
  {
    let def = {
      'current': 1,
      'total': 1
    };

    this.data = {...def, ...this.data};
    this.pages = {};
  }

  buildPages()
  {
    for (let i = this.data.current; i <= this.data.total; i++) {
      this.pages[i - 1] = i;
    }

    this.pages = this.libFuncs.convertObjectToArray(this.pages);
  }

  goToPage(page)
  {
    if (this.data.current != page) {
      this.event.publish('paginate_go_to_page', page);
      this.data.current = page;
    }
  }

  goToNextPage()
  {
    let nextPage = this.data.current + 1;
    if (nextPage <= this.data.total) {
      this.goToPage(nextPage);
    }
  }

  goToPreviousPage()
  {
    let prePage = this.data.current - 1;
    if (prePage >= 1) {
      this.goToPage(prePage);
    }
  }

}
