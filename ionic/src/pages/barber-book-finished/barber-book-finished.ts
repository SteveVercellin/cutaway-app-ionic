import { Component } from '@angular/core';
import { IonicPage, NavParams } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';
import { DomSanitizer } from '@angular/platform-browser';

import { BasePageProvider } from './../../providers/base-page/base-page';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { TranslateProvider } from './../../providers/translate/translate';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { NotifyProvider } from './../../providers/notify/notify';
import { AuthorizeProvider } from './../../providers/authorize/authorize';

/**
 * Generated class for the BarberBookFinishedPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-barber-book-finished',
  templateUrl: 'barber-book-finished.html',
})
export class BarberBookFinishedPage extends BasePageProvider {

  newGroup;

  constructor(
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public nav: NavigationProvider,
    public loadingCtrl: LoadingPopupProvider,
    public notify: NotifyProvider,
    public navParams: NavParams,
    public sanitizer: DomSanitizer,
    public authorize: AuthorizeProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';

    this.newGroup = this.navParams.get('group');
  }

  goToSupportPage()
  {
    this.nav.goToPage('SupportPage');
  }

  goToOrderDetailPage()
  {
    let data = {
      'group_detail': this.newGroup
    }

    this.nav.goToPage('OrdersPage', data);
  }

}
