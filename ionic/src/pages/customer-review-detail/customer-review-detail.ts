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
 * Generated class for the CustomerReviewDetailPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-customer-review-detail',
  templateUrl: 'customer-review-detail.html',
})
export class CustomerReviewDetailPage extends BasePageProvider {

  review;

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
    this.review = null;
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['delete_fail'] = 'Delete customer review failed.';
    this.translateCls.loadTranslateString('DELETE_CUSTOMER_ORDER_REVIEW_FAIL').then(str => {
      this.translateStrings['delete_fail'] = str;
    });
    this.translateStrings['delete_success'] = 'Delete customer review successful.';
    this.translateCls.loadTranslateString('DELETE_CUSTOMER_ORDER_REVIEW_SUCCESS').then(str => {
      this.translateStrings['delete_success'] = str;
    });
  }

  ionViewDidEnter()
  {
    this.loadReview();
  }

  loadReview()
  {
    this.review = this.navParams.get('order');
    this.review['comment'] = '<p>' + this.review['comment'] + '</p>';
    this.review['comment'] = this.libFuncs.makeHtmlTrusted(this.review['comment']);
  }

  deleteReview()
  {
    let that = this;

    let httpCall = that.bookly.deleteCustomerOrderReview(that.review['token']);
    if (httpCall) {
      that.makePageIsLoading();
      let status = '';

      httpCall.subscribe(response => {
        response = that.libFuncs.parseHttpResponse(response);
        if (response['status'] == 'ok') {
          status = 'ok';
        } else {
          status = response['error'] == 'authenticate_fail' ? response['error'] : 'fail';
        }

        that.processAfterDeleteCustomerReview(status);
      }, err => {
        if (that.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
          status = 'authenticate_fail';
        } else {
          status = 'fail';
        }

        that.processAfterDeleteCustomerReview(status);
      });
    }
  }

  processAfterDeleteCustomerReview(status)
  {
    let updatedMsg = '';
    let errMsg = '';
    let closePageLoading = true;

    switch (status) {
      case 'ok':
        updatedMsg = this.translateStrings['delete_success'];
        break;
      case 'authenticate_fail':
        this.clearAuthenticatedStorage();
        closePageLoading = false;
        break;
      case 'fail':
        errMsg = this.translateStrings['delete_fail'];
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
