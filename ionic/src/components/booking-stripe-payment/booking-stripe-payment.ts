import { Component, Input } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { TranslateService } from '@ngx-translate/core';
import { Events, ModalController } from 'ionic-angular';

import { BaseBookingPaymentProvider } from './../../providers/base-booking-payment/base-booking-payment';

import { StripePaymentProvider } from './../../providers/stripe-payment/stripe-payment';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';


/**
 * Generated class for the BookingStripePaymentComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
  selector: 'booking-stripe-payment',
  templateUrl: 'booking-stripe-payment.html'
})
export class BookingStripePaymentComponent extends BaseBookingPaymentProvider {

  @Input() data: any;

  constructor(
    public http: HttpClient,
    public stripeRest: StripePaymentProvider,
    public event: Events,
    public parseErr: ParseErrorProvider,
    public modalCtrl: ModalController,
    public libs: LibFunctionsProvider,
    translate: TranslateService
  ) {
    super(http, stripeRest, event, parseErr, libs);
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

      if (!this.data['group_booking']) {
        this.triggerShowLoadingScreen();
        this.createPendingBooking().then(tryCreatePendingBooking => {
          this.event.publish('booking_after_create_pending_booking', tryCreatePendingBooking);

          if (tryCreatePendingBooking['created']) {
            this.triggerHideLoadingScreen();
            this.beginPayUsingStripe(tryCreatePendingBooking);
          } else {
            this.changeStateProcessing(false);
          }
        });
      } else {
        this.beginPayUsingStripe({
          'created': true,
          'group': this.data['group_booking']
        });
      }
    }
  }

  beginPayUsingStripe(data)
  {
    let that = this;

    that.dataPay['group'] = data['group'];
    let dataPopup = {
      'data_pay': that.dataPay
    };

    let modal = that.modalCtrl.create('StripePaymentPopupPage', dataPopup);
    modal.onDidDismiss(data => {
      that.changeStateProcessing(false);
      that.event.publish('booking_after_stripe_pay', data);
    });
    modal.present();
  }

}
