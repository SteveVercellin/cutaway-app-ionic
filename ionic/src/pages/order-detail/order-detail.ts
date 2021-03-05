import { Component } from '@angular/core';
import { IonicPage, NavParams, Events } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';
import { CacheService } from "ionic-cache";

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
import { StripePaymentProvider } from './../../providers/stripe-payment/stripe-payment';

/**
 * Generated class for the OrderDetailPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-order-detail',
  templateUrl: 'order-detail.html',
})
export class OrderDetailPage extends BasePageProvider {

  order;
  dataBook;
  cacheKey;
  readOnly;

  constructor(
    public navParams: NavParams,
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public nav: NavigationProvider,
    public libFuncs: LibFunctionsProvider,
    public bookly: BooklyIntergrateProvider,
    public notify: NotifyProvider,
    public loadingCtrl: LoadingPopupProvider,
    public parseErr: ParseErrorProvider,
    public event: Events,
    public cache: CacheService,
    public authorize: AuthorizeProvider,
    public stripeRest: StripePaymentProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';

    this.order = null;
    this.dataBook = null;
    this.readOnly = this.navParams.get('read_only');
    this.readOnly = typeof this.readOnly != 'boolean' ? false : this.readOnly;
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['pay_paypal_fail'] = 'Payment by PayPal failed.';
    this.translateStrings['pay_paypal_success'] = 'Payment by PayPal was successful.';
    this.translateStrings['load_fail'] = 'Loading order detail failed.';
    this.translateStrings['cancel_order_fail'] = 'Cancel order failed.';
    this.translateStrings['cancel_order_success'] = 'Cancel order successful.';
    this.translateStrings['approved'] = 'Approved';
    this.translateStrings['pending'] = 'Pending';
    this.translateStrings['cancelled'] = 'Cancelled';

    this.translateCls.loadTranslateString('PAY_WITH_PAYPAL_FAIL').then(str => {
      this.translateStrings['pay_paypal_fail'] = str;
    });
    this.translateCls.loadTranslateString('PAY_WITH_PAYPAL_SUCCESS').then(str => {
      this.translateStrings['pay_paypal_success'] = str;
    });
    this.translateCls.loadTranslateString('LOAD_CUSTOMER_ORDER_FAIL').then(str => {
      this.translateStrings['load_fail'] = str;
    });
    this.translateCls.loadTranslateString('CANCEL_ORDER_FAIL').then(str => {
      this.translateStrings['cancel_order_fail'] = str;
    });
    this.translateCls.loadTranslateString('CANCEL_ORDER_SUCCESS').then(str => {
      this.translateStrings['cancel_order_success'] = str;
    });
    this.translateCls.loadTranslateString('PAYED').then(str => {
      this.translateStrings['approved'] = str;
    });
    this.translateCls.loadTranslateString('NOT_PAY').then(str => {
      this.translateStrings['pending'] = str;
    });
    this.translateCls.loadTranslateString('CANCELLED').then(str => {
      this.translateStrings['cancelled'] = str;
    });
  }

  ionViewDidEnter()
  {
    this.bookly.loadAuthenticatedData().then(() => {
      this.loadOrderDetail();
    });
  }

  loadOrderDetail()
  {
    let orderGroup = this.navParams.get('group');
    let httpCall = this.bookly.loadCustomerOrder(orderGroup);

    if (httpCall) {
      this.cacheKey = httpCall.cache_key;
      this.makePageIsLoading();
      let status = '';

      this.cache.loadFromObservable(this.cacheKey, httpCall.http).subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          if (response['data'].hasOwnProperty('order')) {
            this.order = response['data']['order'];

            this.dataBook = {
              'data_book': {
                'barber': this.order['barber'],
                'services': this.order['services'],
                'date': this.order['date'],
                'time': this.order['time'],
                'number_people': this.order['number_people']
              },
              'data_load_service': {},
              'group_booking': this.order['group_booking']
            };

            this.order['services'] = this.libFuncs.convertObjectToArray(this.order['services']);
            this.order['notes'] = this.libFuncs.convertObjectToArray(this.order['notes']);
            this.order['date']['day'] = parseInt(this.order['date']['day']);
            this.order['group'] = orderGroup;

            status = 'ok';
          } else {
            status = 'fail';
          }
        } else {
          status = response['error'];
        }

        this.processAfterLoadOrder(status);
      }, err => {
        if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
          status = 'authenticate_fail';
        } else {
          status = 'fail';
        }

        this.processAfterLoadOrder(status);
      });
    } else {
      this.processAfterLoadOrder('fail');
    }
  }

  processAfterLoadOrder(status)
  {
    let loaded = false;
    let errMsg = '';
    let clearCache = true;
    let closePageLoading = true;

    switch (status) {
      case 'ok':
        clearCache = false;
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

    if (clearCache && this.cacheKey != '') {
      this.cache.removeItem(this.cacheKey);
    }

    if (closePageLoading) {
      this.makePageIsNotLoading();
    }

    if (loaded) {
      this.setupEvent();
    } else if (errMsg != '') {
      let that = this;

      that.makePageIsNotLoading();
      let alertBox = that.notify.warningAlert(errMsg);
      alertBox.onDidDismiss(function (data, role) {
        that.nav.popBack();
      });
    }
  }

  setupEvent()
  {
    let that = this;

    this.event.unsubscribe('booking_show_loading_screen');
    this.event.subscribe('booking_show_loading_screen', function () {
      that.makePageIsLoading();
    });
    this.event.unsubscribe('booking_hide_loading_screen');
    this.event.subscribe('booking_hide_loading_screen', function () {
      that.makePageIsNotLoading();
    });

    this.event.unsubscribe('booking_after_stripe_pay');
    this.event.subscribe('booking_after_stripe_pay', function (data) {
      if (that.cacheKey != '') {
        that.cache.removeItem(that.cacheKey);
      }

      if (data.status == 'paid') {
        that.order.status = 'approved';
      } else if (data.status == 'authenticate_fail') {
        that.clearAuthenticatedStorage();
      }
    });

    this.event.unsubscribe('booking_after_paypal_pay');
    this.event.subscribe('booking_after_paypal_pay', function (data) {
      if (that.cacheKey != '') {
        that.cache.removeItem(that.cacheKey);
      }

      if (data.status == 'paid') {
        that.notify.notifyAlert(that.translateStrings['pay_paypal_success']);
        that.order.status = 'approved';
      } else if (data.status == 'authenticate_fail') {
        that.clearAuthenticatedStorage();
      } else {
        that.notify.warningAlert(that.translateStrings['pay_paypal_fail']);
      }
    });
  }

  cancelOrder(order)
  {
    if (!this.readOnly) {
      let orderGroup = order.group;
      this.makePageIsLoading();

      this.stripeRest.loadAuthenticatedData().then(() => {
        let dataCancel = {'group': orderGroup};
        let httpCall = this.stripeRest.refund(dataCancel);
        if (httpCall) {
          let status = '';

          httpCall.subscribe(response => {
            response = this.libFuncs.parseHttpResponse(response);

            if (response['status'] == 'ok') {
              status = 'ok';
            } else {
              status = response['error'];
            }

            this.processAfterCancelOrder(status);
          }, err => {
            if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
              status = 'authenticate_fail';
            } else {
              status = 'fail';
            }

            this.processAfterCancelOrder(status);
          });
        } else {
          this.processAfterCancelOrder('fail');
        }
      }, err => {
        this.processAfterCancelOrder('fail');
      });
    }
  }

  processAfterCancelOrder(status)
  {
    let cancelled = false;
    let errMsg = '';
    let closePageLoading = true;

    switch (status) {
      case 'ok':
        cancelled = true;
        break;
      case 'authenticate_fail':
        this.clearAuthenticatedStorage();
        closePageLoading = false;
        break;
      case 'fail':
      default:
        errMsg = this.translateStrings['cancel_order_fail'];
        break;
    }

    if (closePageLoading) {
      this.makePageIsNotLoading();
    }

    if (cancelled) {
      let that = this;

      let alertBox = that.notify.warningAlert(this.translateStrings['cancel_order_success']);
      alertBox.onDidDismiss(function (data, role) {
        that.nav.popBack();
      });
    } else if (errMsg != '') {
      this.notify.warningAlert(errMsg);
    }
  }

}
