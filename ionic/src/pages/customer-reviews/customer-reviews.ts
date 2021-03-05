import { Component } from '@angular/core';
import { IonicPage, NavParams } from 'ionic-angular';
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
 * Generated class for the CustomerReviewsPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-customer-reviews',
  templateUrl: 'customer-reviews.html',
})
export class CustomerReviewsPage extends BasePageProvider {

  needReviewOrders;
  reviewedOrders;
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
    public authorize: AuthorizeProvider,
    public bookly: BooklyIntergrateProvider,
    public parseErr: ParseErrorProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';

    this.needReviewOrders = null;
    this.reviewedOrders = null;
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

    let httpCall = this.bookly.loadCustomerOrdersReview(this.page);
    if (httpCall) {
      this.makePageIsLoading();
      let status = '';

      httpCall.subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          if (response['data'].hasOwnProperty('orders')) {
            this.needReviewOrders = new Array();
            this.reviewedOrders = new Array();
            let customerOrders = response['data']['orders'];

            for (let key in customerOrders) {
              if (customerOrders.hasOwnProperty(key)) {
                for (let index in customerOrders[key]) {
                  if (customerOrders[key].hasOwnProperty(index)) {
                    if (key == 'need_review') {
                      this.needReviewOrders[this.needReviewOrders.length] = customerOrders[key][index];
                    } else if (key == 'reviewed') {
                      this.reviewedOrders[this.reviewedOrders.length] = customerOrders[key][index];
                    }
                  }
                }
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

  doLoadMore(infiniteScroll)
  {
    if (!this.loadEnd) {
      this.page++;

      let httpCall = this.bookly.loadCustomerOrdersReview(this.page);
      if (httpCall) {
        let status = '';

        httpCall.subscribe(response => {
          response = this.libFuncs.parseHttpResponse(response);

          if (response['status'] == 'ok') {
            if (response['data'].hasOwnProperty('orders')) {
              let customerOrders = response['data']['orders'];

              let key = 'reviewed';
              for (let index in customerOrders[key]) {
                if (customerOrders[key].hasOwnProperty(index)) {
                  this.reviewedOrders[this.reviewedOrders.length] = customerOrders[key][index];
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

          this.processAfterLoadOrdersReview(status, infiniteScroll);
        }, err => {
          if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
            status = 'authenticate_fail';
          } else {
            status = 'fail';
          }

          this.processAfterLoadOrdersReview(status, infiniteScroll);
        });
      } else {
        this.processAfterLoadOrdersReview('fail', infiniteScroll);
      }
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

      this.nav.pushToPage('CustomerReviewDetailPage', {
        'order': order
      });
    }
  }

  goToLeaveReview(order)
  {
    if (!this.checkPushedToNewPage()) {
      this.changeStatusPushToNewPage(true);

      this.nav.pushToPage('CustomerLeaveReviewPage', {
        'order': order
      });
    }
  }

}
