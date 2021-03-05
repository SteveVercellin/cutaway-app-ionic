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
 * Generated class for the BarberReviewsPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-barber-reviews',
  templateUrl: 'barber-reviews.html',
})
export class BarberReviewsPage extends BasePageProvider {

  reviews;
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

    this.reviews = null;
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['load_fail'] = 'Loading orders failed.';
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

    this.processLoadOrdersReview(event);
  }

  doLoadMore(infiniteScroll)
  {
    if (!this.loadEnd) {
      this.page++;

      this.processLoadOrdersReview(infiniteScroll);
    }
  }

  processLoadOrdersReview(event)
  {
    let httpCall = this.bookly.loadStaffOrdersReview(this.page);
    if (httpCall) {
      this.makePageIsLoading();
      let status = '';

      httpCall.subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          if (response['data'].hasOwnProperty('orders')) {
            let customerOrders = response['data']['orders'];
            if (event == null) {
              this.reviews = new Array();
            }

            for (let index in customerOrders) {
              if (customerOrders.hasOwnProperty(index)) {
                this.reviews.push(customerOrders[index]);
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

        this.processAfterLoadOrdersReview(status, event);
      }, err => {
        if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
          status = 'authenticate_fail';
        } else {
          status = 'fail';
        }

        this.processAfterLoadOrdersReview(status, event);
      });
    } else {
      this.processAfterLoadOrdersReview('fail', event);
    }
  }

  processAfterLoadOrdersReview(status, event)
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

  goToReviewDetail(order)
  {
    if (!this.checkPushedToNewPage()) {
      this.changeStatusPushToNewPage(true);

      this.nav.pushToPage('BarberReviewDetailPage', {
        'order': order
      });
    }
  }

}
