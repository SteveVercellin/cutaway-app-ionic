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
import { BooklyIntergrateProvider } from './../../providers/bookly-intergrate/bookly-intergrate';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { AuthorizeProvider } from './../../providers/authorize/authorize';

/**
 * Generated class for the CustomerFavouriteBarbersPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-customer-favourite-barbers',
  templateUrl: 'customer-favourite-barbers.html',
})
export class CustomerFavouriteBarbersPage extends BasePageProvider {

  barbers;

  constructor(
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public loadingCtrl: LoadingPopupProvider,
    public navParams: NavParams,
    public libFuncs: LibFunctionsProvider,
    public nav: NavigationProvider,
    public notify: NotifyProvider,
    public bookly: BooklyIntergrateProvider,
    public parseErr: ParseErrorProvider,
    public event: Events,
    public authorize: AuthorizeProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';
    this.barbers = null;
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['load_data_fail'] = 'Loading data failed.';
    this.translateStrings['mark_favourite_barber_fail'] = 'Mark barber to your favourite barber failed.';
    this.translateStrings['mark_favourite_barber_success'] = 'Marked barber to your favourite barber.';
    this.translateStrings['mark_unfavourite_barber_fail'] = 'Mark barber to your unfavourite barber failed.';
    this.translateStrings['mark_unfavourite_barber_success'] = 'Marked barber to your unfavourite barber.';

    this.translateCls.loadTranslateString('LOAD_STAFFS_CUSTOMER_BOOKED_FAIL').then(str => {
      this.translateStrings['load_data_fail'] = str;
    });

    this.translateCls.loadTranslateString('MARK_FAVOURITE_STAFF_FAIL').then(str => {
      this.translateStrings['mark_favourite_barber_fail'] = str;
    });

    this.translateCls.loadTranslateString('MARK_FAVOURITE_STAFF_SUCCESS').then(str => {
      this.translateStrings['mark_favourite_barber_success'] = str;
    });

    this.translateCls.loadTranslateString('MARK_UNFAVOURITE_STAFF_FAIL').then(str => {
      this.translateStrings['mark_unfavourite_barber_fail'] = str;
    });

    this.translateCls.loadTranslateString('MARK_UNFAVOURITE_STAFF_SUCCESS').then(str => {
      this.translateStrings['mark_unfavourite_barber_success'] = str;
    });
  }

  ionViewDidEnter()
  {
    this.bookly.loadAuthenticatedData().then(() => {
      this.setupData(null);
    });
  }

  setupData(event)
  {
    let httpCall = this.bookly.loadListStaffsCustomerBooked();
    if (httpCall) {
      this.makePageIsLoading();
      let status = '';

      httpCall.subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          if (response['data'].hasOwnProperty('staffs')) {
            this.barbers = this.libFuncs.convertObjectToArray(response['data']['staffs']);
            if (this.barbers.length) {
              for (let i = 0; i < this.barbers.length; i++) {
                this.barbers[i]['favourite'] = this.barbers[i]['is_favorite'] ? 'yes' : 'no';
              }
            }
            status = 'ok';
          } else {
            status = 'fail';
          }
        } else {
          status = response['error'];
        }

        this.processAfterLoadBarbers(status, event);
      }, err => {
        if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
          status = 'authenticate_fail';
        } else {
          status = 'fail';
        }

        this.processAfterLoadBarbers(status, event);
      });
    } else {
      this.processAfterLoadBarbers('fail', event);
    }
  }

  processAfterLoadBarbers(status, event)
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
        this.barbers = new Array();
        errMsg = this.translateStrings['load_data_fail'];
        break;
    }

    if (closePageLoading) {
      this.makePageIsNotLoading();
    }

    if (loaded) {
      if (event == null) {
        this.setupEvents();
      } else {
        event.complete();
      }
    } else if (errMsg != '') {
      this.notify.warningAlert(errMsg);
    }
  }

  setupEvents()
  {
    let that = this;

    this.event.unsubscribe('barber_box_make_barber_favourite');
    this.event.subscribe('barber_box_make_barber_favourite', function (barber) {
      let httpCall = that.bookly.makeStaffFavourite(barber.id);
      if (httpCall) {
        that.makePageIsLoading();
        let status = '';

        httpCall.subscribe(response => {
          response = that.libFuncs.parseHttpResponse(response);

          if (response['status'] == 'ok') {
            barber.favourite = 'yes';
            status = 'mark_ok';
          } else {
            status = response['error'] == 'authenticate_fail' ? response['error'] : 'mark_fail';
          }

          that.processAfterChangeBarberStatus(status);
        }, err => {
          if (that.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
            status = 'authenticate_fail';
          } else {
            status = 'mark_fail';
          }

          that.processAfterChangeBarberStatus(status);
        })
      } else {
        that.processAfterChangeBarberStatus('mark_fail');
      }
    });

    this.event.unsubscribe('barber_box_make_barber_unfavourite');
    this.event.subscribe('barber_box_make_barber_unfavourite', function (barber) {
      let httpCall = that.bookly.makeStaffUnfavourite(barber.id);
      if (httpCall) {
        that.makePageIsLoading();
        let status = '';

        httpCall.subscribe(response => {
          response = that.libFuncs.parseHttpResponse(response);

          if (response['status'] == 'ok') {
            barber.favourite = 'no';
            status = 'unmark_ok';
          } else {
            status = response['error'] == 'authenticate_fail' ? response['error'] : 'unmark_fail';
          }

          that.processAfterChangeBarberStatus(status);
        }, err => {
          if (that.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
            status = 'authenticate_fail';
          } else {
            status = 'unmark_fail';
          }

          that.processAfterChangeBarberStatus(status);
        })
      } else {
        that.processAfterChangeBarberStatus('unmark_fail');
      }
    });
  }

  processAfterChangeBarberStatus(status)
  {
    let updatedMsg = '';
    let errMsg = '';
    let closePageLoading = true;

    switch (status) {
      case 'mark_ok':
        updatedMsg = this.translateStrings['mark_favourite_barber_success'];
        break;
      case 'unmark_ok':
        updatedMsg = this.translateStrings['mark_unfavourite_barber_success'];
        break;
      case 'authenticate_fail':
        this.clearAuthenticatedStorage();
        closePageLoading = false;
        break;
      case 'mark_fail':
        errMsg = this.translateStrings['mark_favourite_barber_fail'];
        break;
      case 'unmark_fail':
        errMsg = this.translateStrings['mark_unfavourite_barber_fail'];
        break;
    }


    if (closePageLoading) {
      this.makePageIsNotLoading();
    }

    if (updatedMsg != '') {
      this.notify.notifyToast(updatedMsg);
    } else if (errMsg != '') {
      this.notify.warningAlert(errMsg);
    }
  }

}
