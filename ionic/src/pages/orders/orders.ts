import { Component } from '@angular/core';
import { IonicPage, NavParams } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';

import { BasePageProvider } from './../../providers/base-page/base-page';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { TranslateProvider } from './../../providers/translate/translate';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';
import { BooklyIntergrateProvider } from './../../providers/bookly-intergrate/bookly-intergrate';
import { NotifyProvider } from './../../providers/notify/notify';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { AuthorizeProvider } from './../../providers/authorize/authorize';

/**
 * Generated class for the OrdersPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-orders',
  templateUrl: 'orders.html',
})
export class OrdersPage extends BasePageProvider {

  pendingOrders;
  completedOrders;
  page;
  loadEnd;
  groupDetail;

  constructor(
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public nav: NavigationProvider,
    public libFuncs: LibFunctionsProvider,
    public bookly: BooklyIntergrateProvider,
    public notify: NotifyProvider,
    public loadingCtrl: LoadingPopupProvider,
    public parseErr: ParseErrorProvider,
    public authorize: AuthorizeProvider,
    public navParams: NavParams,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';

    this.pendingOrders = null;
    this.completedOrders = null;
    this.groupDetail = this.navParams.get('group_detail');
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['pending_order'] = 'Pending orders.';
    this.translateStrings['processing_order'] = 'Processing orders.';
    this.translateStrings['completed_order'] = 'Completed orders.';
    this.translateStrings['load_fail'] = 'Loading orders failed.';

    this.translateCls.loadTranslateString('ORDERS_PENDING').then(str => {
      this.translateStrings['pending_order'] = str;
    });
    this.translateCls.loadTranslateString('ORDERS_PROCESSING').then(str => {
      this.translateStrings['processing_order'] = str;
    });
    this.translateCls.loadTranslateString('ORDERS_COMPLETED').then(str => {
      this.translateStrings['completed_order'] = str;
    });
    this.translateCls.loadTranslateString('LOAD_CUSTOMER_ORDERS_FAIL').then(str => {
      this.translateStrings['load_fail'] = str;
    });
  }

  ionViewDidEnter()
  {
    this.changeStatusPushToNewPage(false);
    this.bookly.loadAuthenticatedData().then(() => {
      let groupJump = 0;

      if (typeof this.groupDetail != 'undefined') {
        this.groupDetail = parseInt(this.groupDetail);
        if (this.groupDetail > 0) {
          groupJump = this.groupDetail;
        }
      }

      if (groupJump > 0) {
        this.groupDetail = 0;
        this.goToOrderDetail({group: groupJump});
      } else {
        this.setupData(null);
      }
    });
  }

  setupData(event)
  {
    this.page = 1;
    this.loadEnd = false;

    let httpCall = this.bookly.loadCustomerOrders(this.page);
    if (httpCall) {
      this.makePageIsLoading();
      let status = '';

      httpCall.subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          if (response['data'].hasOwnProperty('orders')) {
            this.pendingOrders = new Array();
            this.completedOrders = new Array();
            let customerOrders = response['data']['orders'];

            for (let key in customerOrders) {
              if (customerOrders.hasOwnProperty(key)) {
                for (let index in customerOrders[key]) {
                  if (customerOrders[key].hasOwnProperty(index)) {
                    let date = customerOrders[key][index]['date']['year'] + '-' + customerOrders[key][index]['date']['month'] + '-' + customerOrders[key][index]['date']['day'];
                    let time = date + ' ' + customerOrders[key][index]['time'];
                    let order = {
                      'group': customerOrders[key][index]['group'],
                      'barber': {
                        'name': customerOrders[key][index]['barber']['full_name']
                      },
                      'service': {
                        'price': customerOrders[key][index]['services']['price']
                      },
                      'time': time
                    };

                    if (key == 'pending') {
                      this.pendingOrders[this.pendingOrders.length] = order;
                    } else if (key == 'approved') {
                      this.completedOrders[this.completedOrders.length] = order;
                    }
                  }
                }
              }
            }

            if (!this.completedOrders.length) {
              this.loadEnd = true;
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

      let httpCall = this.bookly.loadCustomerOrders(this.page);

      if (httpCall) {
        let status = '';

        httpCall.subscribe(response => {
          response = this.libFuncs.parseHttpResponse(response);

          if (response['status'] == 'ok') {
            if (response['data'].hasOwnProperty('orders')) {
              let customerOrders = response['data']['orders'];
              let end = response['data'].hasOwnProperty('end') ? response['data']['end'] : true;

              if (!end) {
                let key = 'approved';
                for (let index in customerOrders[key]) {
                  if (customerOrders[key].hasOwnProperty(index)) {
                    let date = customerOrders[key][index]['date']['year'] + '-' + customerOrders[key][index]['date']['month'] + '-' + customerOrders[key][index]['date']['day'];
                    let time = date + ' ' + customerOrders[key][index]['time'];
                    let order = {
                      'group': customerOrders[key][index]['group'],
                      'barber': {
                        'name': customerOrders[key][index]['barber']['full_name']
                      },
                      'service': {
                        'price': customerOrders[key][index]['services']['price']
                      },
                      'time': time
                    };

                    this.completedOrders.push(order);
                  }
                }
              } else {
                this.loadEnd = true;
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
        'group': order['group']
      }

      this.nav.pushToPage('OrderDetailPage', data);
    }
  }

}
