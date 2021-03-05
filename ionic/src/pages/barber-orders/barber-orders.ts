import { Component } from '@angular/core';
import { IonicPage } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';

import { BasePageProvider } from './../../providers/base-page/base-page';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { TranslateProvider } from './../../providers/translate/translate';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { NotifyProvider } from './../../providers/notify/notify';
import { BooklyIntergrateProvider } from './../../providers/bookly-intergrate/bookly-intergrate';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { AuthorizeProvider } from './../../providers/authorize/authorize';

/**
 * Generated class for the BarberOrdersPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-barber-orders',
  templateUrl: 'barber-orders.html',
})
export class BarberOrdersPage extends BasePageProvider {

  orders;
  page;
  loadEnd;

  constructor(
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public loadingCtrl: LoadingPopupProvider,
    public nav: NavigationProvider,
    public notify: NotifyProvider,
    public bookly: BooklyIntergrateProvider,
    public parseErr: ParseErrorProvider,
    public libFuncs: LibFunctionsProvider,
    public authorize: AuthorizeProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'staff';

    this.orders = null;
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['cancelled_status'] = 'Cancelled';
    this.translateStrings['pending_status'] = 'Not pay';
    this.translateStrings['approved_status'] = 'Payed';
    this.translateStrings['load_fail'] = 'Loading orders failed.';

    this.translateCls.loadTranslateString('CANCELLED').then(str => {
      this.translateStrings['cancelled_status'] = str;
    });
    this.translateCls.loadTranslateString('NOT_PAY').then(str => {
      this.translateStrings['pending_status'] = str;
    });
    this.translateCls.loadTranslateString('PAYED').then(str => {
      this.translateStrings['approved_status'] = str;
    });
    this.translateCls.loadTranslateString('LOAD_CUSTOMER_ORDERS_FAIL').then(str => {
      this.translateStrings['load_fail'] = str;
    });
  }

  ionViewDidEnter()
  {
    this.changeStatusPushToNewPage(false);
    this.bookly.loadAuthenticatedData().then(() => {
      this.setupData(null);
    });
  }

  setupData(event)
  {
    this.page = 1;
    this.loadEnd = false;

    let httpCall = this.bookly.getStaffOrders(this.page);
    if (httpCall) {
      this.makePageIsLoading();
      let status = '';

      httpCall.subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          if (response['data'].hasOwnProperty('orders')) {
            this.orders = [];
            let staffOrders = response['data']['orders'];

            for (let key in staffOrders) {
              if (staffOrders.hasOwnProperty(key)) {
                let order = staffOrders[key];
                order = this.formatOrder(order);

                this.orders[this.orders.length] = order;
              }
            }

            if (response['data'].hasOwnProperty('end')) {
              this.loadEnd = response['data']['end'];
            }

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

  doLoadMore(infiniteScroll)
  {
    if (!this.loadEnd) {
      this.page++;

      let httpCall = this.bookly.getStaffOrders(this.page);

      if (httpCall) {
        let status = '';

        httpCall.subscribe(response => {
          response = this.libFuncs.parseHttpResponse(response);

          if (response['status'] == 'ok') {
            if (response['data'].hasOwnProperty('orders')) {
              let staffOrders = response['data']['orders'];

              for (let key in staffOrders) {
                if (staffOrders.hasOwnProperty(key)) {
                  let order = staffOrders[key];
                  order = this.formatOrder(order);

                  this.orders.push(order);
                }
              }

              if (response['data'].hasOwnProperty('end')) {
                this.loadEnd = response['data']['end'];
              }

              status = 'ok';
            } else {
              status = 'fail';
            }
          } else {
            status = response['error'];
          }

          this.processAfterLoadOrders(status, infiniteScroll);
        }, err => {
          if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
            status = 'authenticate_fail';
          } else {
            status = 'fail';
          }

          this.processAfterLoadOrders(status, infiniteScroll);
        });
      } else {
        this.processAfterLoadOrders('fail', infiniteScroll);
      }
    }
  }

  processAfterLoadOrders(status, event)
  {
    let loaded = false;
    let errMsg = '';
    let closePageLoading = true;

    switch (status) {
      case 'ok':
        loaded = true;
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

    if (loaded) {
      if (event != null) {
        event.complete();
      }
    } else if (errMsg != '') {
      this.notify.warningAlert(errMsg);
    }
  }

  goToOrderDetail(order)
  {
    if (!this.checkPushedToNewPage()) {
      this.changeStatusPushToNewPage(true);

      let data = {
        'id': order.id
      }

      this.nav.pushToPage('BarberOrderDetailPage', data);
    }
  }

  formatOrder(order)
  {
    if (this.translateStrings.hasOwnProperty(order['status'] + '_status')) {
      order['status'] = this.translateStrings[order['status'] + '_status'];
    }
    order['display'] = order['customer']['full_name'] + ' - ';
    order['display'] += order['services']['name'] + ' - ';
    order['display'] += order['date'] + ' - ';
    order['display'] += order['status'];

    return order;
  }

}
