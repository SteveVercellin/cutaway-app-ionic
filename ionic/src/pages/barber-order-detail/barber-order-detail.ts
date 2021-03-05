import { Component } from '@angular/core';
import { IonicPage, NavParams, Events } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';

import { BasePageProvider } from './../../providers/base-page/base-page';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { TranslateProvider } from './../../providers/translate/translate';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { NotifyProvider } from './../../providers/notify/notify';
import { AuthorizeProvider } from './../../providers/authorize/authorize';
import { BooklyIntergrateProvider } from './../../providers/bookly-intergrate/bookly-intergrate';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';

/**
 * Generated class for the BarberOrderDetailPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-barber-order-detail',
  templateUrl: 'barber-order-detail.html',
})
export class BarberOrderDetailPage extends BasePageProvider {

  order;

  constructor(
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public loadingCtrl: LoadingPopupProvider,
    public navParams: NavParams,
    public libFuncs: LibFunctionsProvider,
    public nav: NavigationProvider,
    public notify: NotifyProvider,
    public authorize: AuthorizeProvider,
    public event: Events,
    public bookly: BooklyIntergrateProvider,
    public parseErr: ParseErrorProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'staff';

    this.order = null;
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['load_fail'] = 'Loading order failed.';
    this.translateCls.loadTranslateString('LOAD_CUSTOMER_ORDERS_FAIL').then(str => {
      this.translateStrings['load_fail'] = str;
    });
  }

  ionViewDidEnter()
  {
    this.bookly.loadAuthenticatedData().then(() => {
      this.setupData();
    });
  }

  setupData()
  {
    let orderId = this.navParams.get('id');

    let httpCall = this.bookly.getStaffOrder(orderId);
    if (httpCall) {
      this.makePageIsLoading();
      let status = '';

      httpCall.subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          if (response['data'].hasOwnProperty('order')) {
            this.order = response['data']['order'];

            this.order.times_list = this.libFuncs.splitListToBlock(this.order.times_list);
            this.order.services = this.libFuncs.convertObjectToArray(this.order.services);

            status = 'ok';
          } else {
            status = 'fail';
          }
        } else {
          status = response['error'];
        }

        this.processAfterLoadOrders(status, event);
      }, err => {
        if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
          status = 'authenticate_fail';
        } else {
          status = 'fail';
        }

        this.processAfterLoadOrders(status, event);
      });
    } else {
      this.processAfterLoadOrders('fail', event);
    }
  }

  processAfterLoadOrders(status, event)
  {
    let errMsg = '';
    let closePageLoading = true;

    switch (status) {
      case 'ok':
        break;
      case 'authenticate_fail':
        this.clearAuthenticatedStorage();
        closePageLoading = false;
        break;
      case 'fail':
      default:
        errMsg = this.translateStrings['load_fail'];
        break;
    }

    if (closePageLoading) {
      this.makePageIsNotLoading();
    }

    if (errMsg != '') {
      this.notify.warningAlert(errMsg);
    }
  }

}
