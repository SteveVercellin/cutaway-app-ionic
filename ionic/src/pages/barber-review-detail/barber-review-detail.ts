import { Component } from '@angular/core';
import { IonicPage, NavParams } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';

import { BasePageProvider } from '../../providers/base-page/base-page';
import { AuthenticateProvider } from '../../providers/authenticate/authenticate';
import { TranslateProvider } from '../../providers/translate/translate';
import { LoadingPopupProvider } from '../../providers/loading-popup/loading-popup';
import { LibFunctionsProvider } from '../../providers/lib-functions/lib-functions';
import { NavigationProvider } from '../../providers/navigation/navigation';
import { NotifyProvider } from '../../providers/notify/notify';
import { AuthorizeProvider } from '../../providers/authorize/authorize';
import { BooklyIntergrateProvider } from '../../providers/bookly-intergrate/bookly-intergrate';
import { ParseErrorProvider } from '../../providers/parse-error/parse-error';

/**
 * Generated class for the BarberReviewDetailPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-barber-review-detail',
  templateUrl: 'barber-review-detail.html',
})
export class BarberReviewDetailPage extends BasePageProvider {

  review;

  constructor(
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public loadingCtrl: LoadingPopupProvider,
    public navParams: NavParams,
    public libFuncs: LibFunctionsProvider,
    public nav: NavigationProvider,
    public notify: NotifyProvider,
    public authorize: AuthorizeProvider,
    public bookly: BooklyIntergrateProvider,
    public parseErr: ParseErrorProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'staff';
    this.review = null;
  }

  ionViewDidLoad()
  {
    this.loadData();
  }

  ionViewDidEnter()
  {
    this.loadReview();
  }

  loadReview()
  {
    this.review = this.navParams.get('order');
    this.review['rating']['comment'] = '<p>' + this.review['rating']['comment'] + '</p>';
    this.review['rating']['comment'] = this.libFuncs.makeHtmlTrusted(this.review['rating']['comment']);
  }

}
