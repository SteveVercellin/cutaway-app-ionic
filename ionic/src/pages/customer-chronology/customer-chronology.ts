import { Component } from '@angular/core';
import { IonicPage, NavParams } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';
import { Events } from 'ionic-angular';

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
 * Generated class for the CustomerChronologyPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-customer-chronology',
  templateUrl: 'customer-chronology.html',
})
export class CustomerChronologyPage extends BasePageProvider {

  customerHistories;
  monthNames;
  page;
  loadEnd;

  constructor(
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public loadingCtrl: LoadingPopupProvider,
    public navParams: NavParams,
    public libFuncs: LibFunctionsProvider,
    public nav: NavigationProvider,
    public notify: NotifyProvider,
    public event: Events,
    public authorize: AuthorizeProvider,
    public bookly: BooklyIntergrateProvider,
    public parseErr: ParseErrorProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';
    this.customerHistories = null;
    this.monthNames = {};
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.monthNames = {
      0: 'January',
      1: 'February',
      2: 'March',
      3: 'April',
      4: 'May',
      5: 'June',
      6: 'July',
      7: 'August',
      8: 'September',
      9: 'October',
      10: 'November',
      11: 'December'
    };
    this.translateStrings['load_fail'] = 'Loading histories failed.';

    this.translateCls.loadTranslateString('JANUARY_LONG').then(str => {
      this.monthNames[0] = str;
    });
    this.translateCls.loadTranslateString('FEBRUARY_LONG').then(str => {
      this.monthNames[1] = str;
    });
    this.translateCls.loadTranslateString('MARCH_LONG').then(str => {
      this.monthNames[2] = str;
    });
    this.translateCls.loadTranslateString('APRIL_LONG').then(str => {
      this.monthNames[3] = str;
    });
    this.translateCls.loadTranslateString('MAY_LONG').then(str => {
      this.monthNames[4] = str;
    });
    this.translateCls.loadTranslateString('JUNE_LONG').then(str => {
      this.monthNames[5] = str;
    });
    this.translateCls.loadTranslateString('JULY_LONG').then(str => {
      this.monthNames[6] = str;
    });
    this.translateCls.loadTranslateString('AUGUST_LONG').then(str => {
      this.monthNames[7] = str;
    });
    this.translateCls.loadTranslateString('SEPTEMBER_LONG').then(str => {
      this.monthNames[8] = str;
    });
    this.translateCls.loadTranslateString('OCTOBER_LONG').then(str => {
      this.monthNames[9] = str;
    });
    this.translateCls.loadTranslateString('NOVEMBER_LONG').then(str => {
      this.monthNames[10] = str;
    });
    this.translateCls.loadTranslateString('DECEMBER_LONG').then(str => {
      this.monthNames[11] = str;
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

    let httpCall = this.bookly.loadCustomerHistories(this.page);
    if (httpCall) {
      this.makePageIsLoading();
      let status = '';

      httpCall.subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          if (response['data'].hasOwnProperty('histories')) {
            this.customerHistories = new Array();
            let customerHistories = response['data']['histories'];

            for (let index in customerHistories) {
              if (customerHistories.hasOwnProperty(index)) {
                let history = {
                  'id': customerHistories[index]['group'],
                  'logo': customerHistories[index]['barber']['avatar'],
                  'name': customerHistories[index]['barber']['full_name'],
                  'date': customerHistories[index]['date']
                };
                history['begin'] = history['date']['day'] + ' ' + this.monthNames[history['date']['month'] - 1] + ' ' + history['date']['year'];

                this.customerHistories.push(history);
              }
            }

            this.loadEnd = response['data'].hasOwnProperty('end') ? response['data']['end'] : true;

            status = 'ok';
          } else {
            status = 'fail';
          }
        } else {
          status = response['error'];
        }

        this.processAfterLoadHistories(status, event);
      }, err => {
        if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
          status = 'authenticate_fail';
        } else {
          status = 'fail';
        }

        this.processAfterLoadHistories(status, event);
      });
    } else {
      this.processAfterLoadHistories('fail', event);
    }
  }

  doLoadMore(infiniteScroll)
  {
    if (!this.loadEnd) {
      this.page++;

      let httpCall = this.bookly.loadCustomerHistories(this.page);

      if (httpCall) {
        let status = '';

        httpCall.subscribe(response => {
          response = this.libFuncs.parseHttpResponse(response);

          if (response['status'] == 'ok') {
            if (response['data'].hasOwnProperty('histories')) {
              this.loadEnd = response['data'].hasOwnProperty('end') ? response['data']['end'] : true;

              if (!this.loadEnd) {
                let customerHistories = response['data']['histories'];

                for (let index in customerHistories) {
                  if (customerHistories.hasOwnProperty(index)) {
                    let history = {
                      'id': customerHistories[index]['group'],
                      'logo': customerHistories[index]['barber']['avatar'],
                      'name': customerHistories[index]['barber']['full_name'],
                      'date': customerHistories[index]['date']
                    };
                    history['begin'] = history['date']['day'] + ' ' + this.monthNames[history['date']['month'] - 1] + ' ' + history['date']['year'];

                    this.customerHistories.push(history);
                  }
                }
              }

              status = 'ok';
            } else {
              status = 'fail';
            }
          } else {
            status = response['error'];
          }

          this.processAfterLoadHistories(status, infiniteScroll);
        }, err => {
          if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
            status = 'authenticate_fail';
          } else {
            status = 'fail';
          }

          this.processAfterLoadHistories(status, infiniteScroll);
        });
      } else {
        this.processAfterLoadHistories('fail', infiniteScroll);
      }
    }
  }

  processAfterLoadHistories(status, event)
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

  goToOrderDetail(history)
  {
    if (!this.checkPushedToNewPage()) {
      this.changeStatusPushToNewPage(true);

      let data = {
        'group': history['id'],
        'read_only': true
      }

      this.nav.pushToPage('OrderDetailPage', data);
    }
  }

}
