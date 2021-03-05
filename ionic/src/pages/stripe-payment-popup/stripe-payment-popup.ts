import { Component } from '@angular/core';
import { IonicPage, NavParams, ViewController } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';
import { Validators, FormBuilder, FormControl } from '@angular/forms';

import { Stripe } from '@ionic-native/stripe';

import { BasePageProvider } from './../../providers/base-page/base-page';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { TranslateProvider } from './../../providers/translate/translate';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { NotifyProvider } from './../../providers/notify/notify';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { DebugProvider } from './../../providers/debug/debug';
import { StripePaymentProvider } from './../../providers/stripe-payment/stripe-payment';
import { AuthorizeProvider } from './../../providers/authorize/authorize';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';

import { EnvConfig } from './../../env';

/**
 * Generated class for the StripePaymentPopupPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-stripe-payment-popup',
  templateUrl: 'stripe-payment-popup.html',
})
export class StripePaymentPopupPage extends BasePageProvider {

  data;
  dataPay;
  months;

  constructor(
    public navParams: NavParams,
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public nav: NavigationProvider,
    public loadingCtrl: LoadingPopupProvider,
    public notify: NotifyProvider,
    public parseErr: ParseErrorProvider,
    public formBuilder: FormBuilder,
    public viewCtrl: ViewController,
    public stripe: Stripe,
    public stripeRest: StripePaymentProvider,
    public debug: DebugProvider,
    public authorize: AuthorizeProvider,
    public libs: LibFunctionsProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';

    this.data = this.navParams.data;
    this.dataPay = this.data['data_pay'];
    this.months = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.stripe.setPublishableKey(EnvConfig.stripe.sandbox.published_key);

    this.translateStrings['card_invalid'] = 'Card is invalid.';
    this.translateStrings['pay_fail'] = 'Payment using Stripe failed.';
    this.translateStrings['pay_success'] = 'Payment using Stripe was successful.';

    this.validationMessages = {
      'card_number': [
        { type: 'required', message: 'Card number is mandatory field.' }
      ],
      'card_expire_month': [
        { type: 'required', message: 'Card expire month is mandatory field.' }
      ],
      'card_expire_year': [
        { type: 'required', message: 'Card expire year is mandatory field.' }
      ],
      'card_cvv': [
        { type: 'required', message: 'Card cvc is mandatory field.' }
      ]
    };

    this.translateCls.loadTranslateString('CREDIT_CARD_INVALID').then(str => {
      this.translateStrings['card_invalid'] = str;
    });
    this.translateCls.loadTranslateString('PAY_WITH_CREDIT_CARD_FAIL').then(str => {
      this.translateStrings['pay_fail'] = str;
    });
    this.translateCls.loadTranslateString('PAY_WITH_CREDIT_CARD_SUCCESS').then(str => {
      this.translateStrings['pay_success'] = str;
    });
    this.translateCls.loadTranslateString('CARD_NUMBER_REQUIRED').then(str => {
      this.validationMessages['card_number'][0]['message'] = str;
    });
    this.translateCls.loadTranslateString('CARD_EXPIRE_MONTH_REQUIRED').then(str => {
      this.validationMessages['card_expire_month'][0]['message'] = str;
    });
    this.translateCls.loadTranslateString('CARD_EXPIRE_YEAR_REQUIRED').then(str => {
      this.validationMessages['card_expire_year'][0]['message'] = str;
    });
    this.translateCls.loadTranslateString('CARD_CVV_REQUIRED').then(str => {
      this.validationMessages['card_cvv'][0]['message'] = str;
    });

    this.validationsForm = this.formBuilder.group({
      card_number: new FormControl('', Validators.compose([
        Validators.required
      ])),
      card_expire_month: new FormControl('', Validators.compose([
        Validators.required
      ])),
      card_expire_year: new FormControl('', Validators.compose([
        Validators.required
      ])),
      card_cvv: new FormControl('', Validators.compose([
        Validators.required
      ]))
    });
  }

  ionViewDidEnter()
  {
    this.stripeRest.loadAuthenticatedData();
  }

  onCancelPay()
  {
    this.viewCtrl.dismiss({
      'status': 'cancel_pay'
    });
    return false;
  }

  onPayUsingCreditCard()
  {
    if (this.checkFormCanSubmit()) {
      this.makePageIsLoading();
      let card = {
        'number': this.validationsForm.value['card_number'],
        'expMonth': this.validationsForm.value['card_expire_month'],
        'expYear': this.validationsForm.value['card_expire_year'],
        'cvc': this.validationsForm.value['card_cvv']
      };

      this.stripe.createCardToken(card).then(cardToken => {
        if (cardToken.hasOwnProperty('id') && cardToken['id'] != '') {
          let response = {
            'nonce': 'pass'
          };
          this.dataPay['nonce'] = response['nonce'];
          this.dataPay['token'] = cardToken['id'];

          let httpCall = this.stripeRest.pay(this.dataPay);
          if (httpCall) {
            let status = '';
            let newGroup = 0;

            httpCall.subscribe(response => {
              response = this.libs.parseHttpResponse(response);

              if (response['status'] == 'ok') {
                status = 'ok';
                if (response['data'].hasOwnProperty('group')) {
                  newGroup = response['data']['group'];
                }
              } else {
                status = response['error'];
              }

              this.processAfterPay(status, newGroup);
            }, err => {
              if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
                status = 'authenticate_fail';
              } else {
                status = 'fail';
              }

              this.processAfterPay(status);
            });
          } else {
            this.processAfterPay('fail',);
          }
        } else {
          this.processAfterPay('card_invalid');
        }
      }, err => {
        this.processAfterPay('card_invalid');
      });
    }
  }

  processAfterPay(status, newGroup = 0)
  {
    let paid = false;
    let errMsg = '';
    let closePageLoading = true;

    switch (status) {
      case 'ok':
        paid = true;
        break;
      case 'authenticate_fail':
        this.viewCtrl.dismiss({
          'status': 'authenticate_fail'
        });
        closePageLoading = false;
        break;
      case 'card_invalid':
        errMsg = this.translateStrings['card_invalid'];
        break;
      case 'fail':
      default:
        errMsg = this.translateStrings['pay_fail'];
        break;
    }

    if (closePageLoading) {
      this.makePageIsNotLoading();
    }

    if (paid) {
      let that = this;

      let notifyBox = that.notify.notifyAlert(that.translateStrings['pay_success']);
      notifyBox.onDidDismiss(function (data, role) {
        that.viewCtrl.dismiss({
          'status': 'paid',
          'group': newGroup
        });
      });
    } else if (errMsg != '') {
      this.notify.warningAlert(errMsg);
    }
  }

}
