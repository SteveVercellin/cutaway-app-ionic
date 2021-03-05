import { Component } from '@angular/core';
import { IonicPage, NavParams, Events } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';

import { BasePageProvider } from './../../providers/base-page/base-page';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { TranslateProvider } from './../../providers/translate/translate';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { BooklyIntergrateProvider } from './../../providers/bookly-intergrate/bookly-intergrate';
import { NotifyProvider } from './../../providers/notify/notify';
import { AuthorizeProvider } from './../../providers/authorize/authorize';

/**
 * Generated class for the BarberBookCompletePage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-barber-book-complete',
  templateUrl: 'barber-book-complete.html',
})
export class BarberBookCompletePage extends BasePageProvider {

  data;
  createdPendingBooking;

  constructor(
    public navParams: NavParams,
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public nav: NavigationProvider,
    public loadingCtrl: LoadingPopupProvider,
    public bookly: BooklyIntergrateProvider,
    public notify: NotifyProvider,
    public event: Events,
    public authorize: AuthorizeProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';

    this.data = this.navParams.data;
    this.createdPendingBooking = false;
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['create_pending_booking_fail'] = 'Book failed.';
    this.translateStrings['created_pending_booking'] = 'Your booking is waiting to pay.';
    this.translateStrings['pay_paypal_fail'] = 'Payment by PayPal failed.';
    this.translateStrings['pay_paypal_success'] = 'Payment by PayPal was successful.';

    this.translateCls.loadTranslateString('CREATE_PENDING_BOOKING_FAIL').then(str => {
      this.translateStrings['create_pending_booking_fail'] = str;
    });

    this.translateCls.loadTranslateString('CREATED_PENDING_BOOKING').then(str => {
      this.translateStrings['created_pending_booking'] = str;
    });

    this.translateCls.loadTranslateString('PAY_WITH_PAYPAL_FAIL').then(str => {
      this.translateStrings['pay_paypal_fail'] = str;
    });

    this.translateCls.loadTranslateString('PAY_WITH_PAYPAL_SUCCESS').then(str => {
      this.translateStrings['pay_paypal_success'] = str;
    });
  }

  ionViewDidEnter()
  {
    let that = this;

    if (this.createdPendingBooking) {
      this.notifyCreatedPendingBooking();
    }

    this.event.unsubscribe('booking_show_loading_screen');
    this.event.subscribe('booking_show_loading_screen', function () {
      that.makePageIsLoading();
    });
    this.event.unsubscribe('booking_hide_loading_screen');
    this.event.subscribe('booking_hide_loading_screen', function () {
      that.makePageIsNotLoading();
    });
    this.event.unsubscribe('booking_after_create_pending_booking');
    this.event.subscribe('booking_after_create_pending_booking', function (data) {
      if (data['created']) {
        that.createdPendingBooking = true;
      } else {
        if (data['error'] == 'authenticate_fail') {
          that.clearAuthenticatedStorage();
        } else {
          that.makePageIsNotLoading();
          that.notify.warningAlert(that.translateStrings['create_pending_booking_fail']);
        }
      }
    });

    this.event.unsubscribe('booking_after_stripe_pay');
    this.event.subscribe('booking_after_stripe_pay', function (result) {
      if (result.status == 'paid') {
        that.nav.goToPage('BarberBookFinishedPage', {group: result.group});
      } else if (result.status == 'authenticate_fail') {
        that.clearAuthenticatedStorage();
      } else {
        that.notifyCreatedPendingBooking();
      }
    });

    this.event.unsubscribe('booking_after_paypal_pay');
    this.event.subscribe('booking_after_paypal_pay', function (result) {
      if (result.status == 'paid') {
        let alertBox = that.notify.notifyAlert(that.translateStrings['pay_paypal_success']);
        alertBox.onDidDismiss(function (data, role) {
          that.nav.goToPage('BarberBookFinishedPage', {group: result.group});
        });
      } else if (result.status == 'authenticate_fail') {
        that.clearAuthenticatedStorage();
      } else {
        let alertBox = that.notify.warningAlert(that.translateStrings['pay_paypal_fail']);
        alertBox.onDidDismiss(function (data, role) {
          that.notifyCreatedPendingBooking();
        });
      }
    });
  }

  notifyCreatedPendingBooking()
  {
    let that = this;

    let alertBox = that.notify.notifyAlert(that.translateStrings['created_pending_booking']);
    alertBox.onDidDismiss(function (data, role) {
      that.nav.goToPage('OrdersPage');
    });
  }

}
