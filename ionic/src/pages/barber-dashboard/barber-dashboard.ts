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
 * Generated class for the BarberDashboardPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-barber-dashboard',
  templateUrl: 'barber-dashboard.html',
})
export class BarberDashboardPage extends BasePageProvider {

  dashboardData: any;
  ignoreToggleEvent;

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
    this.dashboardData = null;
    this.ignoreToggleEvent = false;
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['change_book_available_fail'] = 'Change available book status failed.';
    this.translateStrings['change_book_available_yes_success'] = 'Changed available book status. Available.';
    this.translateStrings['change_book_available_no_success'] = 'Changed available book status. Not Available.';

    this.translateCls.loadTranslateString('CHANGE_BOOK_AVAILABLE_FAIL').then(str => {
      this.translateStrings['change_book_available_fail'] = str;
    });

    this.translateCls.loadTranslateString('CHANGE_BOOK_AVAILABLE_YES_SUCCESS').then(str => {
      this.translateStrings['change_book_available_yes_success'] = str;
    });

    this.translateCls.loadTranslateString('CHANGE_BOOK_AVAILABLE_NO_SUCCESS').then(str => {
      this.translateStrings['change_book_available_no_success'] = str;
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
    let httpCall = this.bookly.getStaffDashboardData();
    if (httpCall) {
      this.makePageIsLoading();
      let status = '';

      httpCall.subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          if (response['data'].hasOwnProperty('data')) {
            let data = response['data']['data'];
            this.dashboardData = {
              'report_price': new Array(),
              'day_to_pay': '',
              'rating': {
                'percent': '',
                'review': ''
              },
              'book_available': null
            };

            if (data.hasOwnProperty('report_price')) {
              this.dashboardData['report_price'] = this.libFuncs.convertObjectToArray(data['report_price']);
            }

            if (data.hasOwnProperty('day_to_pay')) {
              this.dashboardData['day_to_pay'] = data['day_to_pay'];
            }

            if (data.hasOwnProperty('rating')) {
              this.dashboardData['rating'] = data['rating'];
            }

            if (data.hasOwnProperty('book_available')) {
              this.dashboardData['book_available'] = data['book_available'];
            }

            status = 'ok';
          } else {
            status = 'fail';
          }
        } else {
          status = response['error'];
        }

        this.processAfterLoadStaffDashboardData(status, event);
      }, err => {
        if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
          status = 'authenticate_fail';
        } else {
          status = 'fail';
        }

        this.processAfterLoadStaffDashboardData(status, event);
      });
    } else {
      this.processAfterLoadStaffDashboardData('fail', event);
    }
  }

  processAfterLoadStaffDashboardData(status, event)
  {
    let closePageLoading = true;

    switch (status) {
      case 'authenticate_fail':
        this.clearAuthenticatedStorage();
        closePageLoading = false;
        break;
    }

    if (closePageLoading) {
      this.makePageIsNotLoading();
    }

    if (event != null) {
      event.complete();
    }
  }

  changeBookAvailable(ev)
  {
    let currentState = this.dashboardData['book_available'];
    let currentStateInt = currentState ? 1 : 0;
    let prevState = !currentState;

    if (this.ignoreToggleEvent) {
      this.ignoreToggleEvent = false;
      return;
    }

    let httpCall = this.bookly.updateStaffBookAvailable(currentStateInt);
    if (httpCall) {
      this.makePageIsLoading();
      let status = '';

      httpCall.subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          status = 'ok';
        } else {
          status = response['error'];
        }

        this.processAfterChangeBookAvailable(status, ev, prevState);
      }, err => {
        if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
          status = 'authenticate_fail';
        } else {
          status = 'fail';
        }

        this.processAfterChangeBookAvailable(status, ev, prevState);
      });
    } else {
      this.processAfterChangeBookAvailable('fail', ev, prevState);
    }
  }

  processAfterChangeBookAvailable(status, ev, prevState)
  {
    let updated = false;
    let errMsg = '';
    let closePageLoading = true;
    let currentState = this.dashboardData['book_available'];
    let currentStateInt = currentState ? 1 : 0;

    switch (status) {
      case 'ok':
        updated = true;
        break;
      case 'authenticate_fail':
        this.clearAuthenticatedStorage();
        closePageLoading = false;
        break;
      case 'fail':
      default:
        errMsg = this.translateStrings['change_book_available_fail'];
        break;
    }


    if (closePageLoading) {
      this.makePageIsNotLoading();
    }

    if (updated) {
      if (currentStateInt) {
        this.notify.notifyToast(this.translateStrings['change_book_available_yes_success']);
      } else {
        this.notify.notifyToast(this.translateStrings['change_book_available_no_success']);
      }
    } else if (errMsg != '') {
      this.ignoreToggleEvent = true;
      this.dashboardData['book_available'] = prevState;
      ev.checked = prevState;
      this.notify.warningAlert(errMsg);
    }
  }

}
