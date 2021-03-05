import { Component, Input } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { TranslateService } from '@ngx-translate/core';
import { Events } from 'ionic-angular';
import { PayPal, PayPalPayment, PayPalConfiguration } from '@ionic-native/paypal';

import { BaseBookingPaymentProvider } from './../../providers/base-booking-payment/base-booking-payment';

import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { PaypalPaymentProvider } from './../../providers/paypal-payment/paypal-payment';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';

import { EnvConfig } from './../../env';

/**
 * Generated class for the BookingPaypalPaymentComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
  selector: 'booking-paypal-payment',
  templateUrl: 'booking-paypal-payment.html'
})
export class BookingPaypalPaymentComponent extends BaseBookingPaymentProvider {

  @Input() data: any;

  constructor(
    public http: HttpClient,
    public event: Events,
    public parseErr: ParseErrorProvider,
    public payPal: PayPal,
    public paypalRest: PaypalPaymentProvider,
    public libs: LibFunctionsProvider,
    translate: TranslateService
  ) {
    super(http, paypalRest, event, parseErr, libs);
  }

  ngOnChanges()
  {
    this.mergeDataWithDefaultValue();
    this.loadData();
  }

  mergeDataWithDefaultValue()
  {
    let def = {
      'data_book': {
        'barber': {
          'full_name': ''
        },
        'services': {},
        'date': {
          'day': '',
          'month': '',
          'year': ''
        },
        'time': '',
        'number_people': 0
      },
      'data_load_service': {},
      'group_booking': 0,
      'block_times': {}
    };

    this.data = {...def, ...this.data};

    this.dataBook = this.data['data_book'];
    this.dataLoadService = this.data['data_load_service'];
    this.blockTimes = this.data['block_times'];
  }

  pay()
  {
    if (!this.checkIsProcessing()) {
      this.changeStateProcessing(true);

      this.triggerShowLoadingScreen();

      if (!this.data['group_booking']) {
        this.createPendingBooking().then(tryCreatePendingBooking => {
          this.event.publish('booking_after_create_pending_booking', tryCreatePendingBooking);

          if (tryCreatePendingBooking['created']) {
            this.beginPayUsingPaypal(tryCreatePendingBooking);
          } else {
            this.changeStateProcessing(false);
          }
        });
      } else {
        this.beginPayUsingPaypal({
          'created': true,
          'group': this.data['group_booking']
        });
      }
    }
  }

  beginPayUsingPaypal(data)
  {
    this.dataPay['group'] = data['group'];
    let response = {
      'nonce': 'pass'
    };

    this.payPal.init({
      PayPalEnvironmentProduction: EnvConfig.paypal.production.clientId,
      PayPalEnvironmentSandbox: EnvConfig.paypal.sandbox.clientId
    }).then(responsePaypal => {
      this.payPal.prepareToRender('PayPalEnvironmentSandbox', new PayPalConfiguration({
        // Only needed if you get an "Internal Service Error" after PayPal login!
        //payPalShippingAddressOption: 2 // PayPalShippingAddressOptionPayPal
      })).then(responsePaypal => {
        let payment = new PayPalPayment(this.dataPay.amount, this.dataPay.currency, this.dataPay.description, 'sale');
        this.payPal.renderSinglePaymentUI(payment).then(responsePaypal => {
          if (
            responsePaypal.hasOwnProperty('response') &&
            responsePaypal.response.hasOwnProperty('id') &&
            responsePaypal.response.hasOwnProperty('state')
          ) {
            responsePaypal = responsePaypal.response;
            let dataCompleteBook = {
              'pay_id': responsePaypal.id,
              'pay_state': responsePaypal.state,
              'nonce': response['nonce'],
              'group': this.dataPay['group']
            };

            let httpCall = this.paypalRest.completeBooking(dataCompleteBook);
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
              this.processAfterPay('fail');
            }
          } else {
            this.processAfterPay('fail');
          }
        }, err => {
          this.processAfterPay('fail');
        });
      }, err => {
        this.processAfterPay('fail');
      });
    }, err => {
      this.processAfterPay('fail');
    });
  }

  processAfterPay(status, newGroup = 0)
  {
    this.changeStateProcessing(false);
    this.triggerHideLoadingScreen();
    let data = {
      group: newGroup
    }

    switch (status) {
      case 'ok':
        data['status'] = 'paid';
        this.event.publish('booking_after_paypal_pay', data);
        break;
      case 'authenticate_fail':
        data['status'] = 'authenticate_fail';
        this.event.publish('booking_after_paypal_pay', data);
        break;
      case 'fail':
      default:
        data['status'] = 'pay_fail';
        this.event.publish('booking_after_paypal_pay', data);
    }
  }

}
