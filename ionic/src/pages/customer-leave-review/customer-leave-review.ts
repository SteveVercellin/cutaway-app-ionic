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
import { AuthorizeProvider } from './../../providers/authorize/authorize';
import { BooklyIntergrateProvider } from './../../providers/bookly-intergrate/bookly-intergrate';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';

/**
 * Generated class for the CustomerLeaveReviewPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-customer-leave-review',
  templateUrl: 'customer-leave-review.html',
})
export class CustomerLeaveReviewPage extends BasePageProvider {

  review;
  starRating;
  comment;

  constructor(
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public loadingCtrl: LoadingPopupProvider,
    public navParams: NavParams,
    public libFuncs: LibFunctionsProvider,
    public nav: NavigationProvider,
    public notify: NotifyProvider,
    public authorize: AuthorizeProvider,
    public event: Events,
    public bookly: BooklyIntergrateProvider,
    public parseErr: ParseErrorProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';
    this.review = null;
    this.starRating = 0;
    this.comment = '';
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['star_rating_required'] = 'Star rating required.';
    this.translateCls.loadTranslateString('REVIEW_STAR_RATING_REQUIRED').then(str => {
      this.translateStrings['star_rating_required'] = str;
    });
    this.translateStrings['update_fail'] = 'Update customer review failed.';
    this.translateCls.loadTranslateString('UPDATE_CUSTOMER_ORDER_REVIEW_FAIL').then(str => {
      this.translateStrings['update_fail'] = str;
    });
    this.translateStrings['update_success'] = 'Update customer review successful.';
    this.translateCls.loadTranslateString('UPDATE_CUSTOMER_ORDER_REVIEW_SUCCESS').then(str => {
      this.translateStrings['update_success'] = str;
    });
  }

  ionViewDidEnter()
  {
    let that = this;

    that.bookly.loadAuthenticatedData().then(() => {
      that.loadReview();
    });
    that.event.unsubscribe('star_rating_new_rate');
    that.event.subscribe('star_rating_new_rate', function (inputStars) {
      that.starRating = inputStars;
    });
  }

  loadReview()
  {
    this.review = {
      'reviews': {
        'total': 0,
        'items': []
      }
    };

    this.review = {...this.navParams.get('order'), ...this.review};

    this.review.times_list = this.libFuncs.splitListToBlock(this.review.times_list);
    this.review.services = this.libFuncs.convertObjectToArray(this.review.services);

    let staff = this.review['barber']['id'];
    let services = '';

    for (var key in this.review['services']) {
      if (this.review['services'].hasOwnProperty(key) && this.review['services'][key].hasOwnProperty('id')) {
        let separator = '';
        if (services != '') {
          separator = ',';
        }

        services += separator + this.review['services'][key]['id'];
      }
    }

    let httpCall = this.bookly.loadOrdersReview(staff, services);
    if (httpCall) {
      this.makePageIsLoading();

      httpCall.subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          if (response['data'].hasOwnProperty('reviews')) {
            this.review['reviews']['items'] = this.libFuncs.convertObjectToArray(response['data']['reviews']);;
            this.review['reviews']['total'] = this.review['reviews']['items'].length;

            for (var key = 0; key < this.review['reviews']['items'].length; key++) {
              this.review.reviews.items[key]['review'] = this.libFuncs.makeHtmlTrusted(this.review.reviews.items[key]['review']);
            }
          }
        }

        this.makePageIsNotLoading();
      }, err => {
        this.makePageIsNotLoading();
      });
    }
  }

  sendReview()
  {
    let that = this;

    if (that.starRating > 0) {
      let httpCall = that.bookly.updateCustomerOrderReview(that.review['token'], that.starRating, that.comment);
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

          that.processAfterUpdateCustomerReview(status);
        }, err => {
          if (that.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
            status = 'authenticate_fail';
          } else {
            status = 'fail';
          }

          that.processAfterUpdateCustomerReview(status);
        });
      }
    } else {
      that.notify.warningAlert(that.translateStrings['star_rating_required']);
    }
  }

  processAfterUpdateCustomerReview(status)
  {
    let updatedMsg = '';
    let errMsg = '';
    let closePageLoading = true;

    switch (status) {
      case 'ok':
        updatedMsg = this.translateStrings['update_success'];
        break;
      case 'authenticate_fail':
        this.clearAuthenticatedStorage();
        closePageLoading = false;
        break;
      case 'fail':
        errMsg = this.translateStrings['update_fail'];
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
