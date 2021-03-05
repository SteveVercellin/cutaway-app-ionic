import { HelpComponentModule } from './../../components/help/help.module';
import { BookingStripePaymentComponentModule } from './../../components/booking-stripe-payment/booking-stripe-payment.module';
import { BookingPaypalPaymentComponentModule } from './../../components/booking-paypal-payment/booking-paypal-payment.module';
import { BackButtonComponentModule } from './../../components/back-button/back-button.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { BarberBookCompletePage } from './barber-book-complete';

@NgModule({
  declarations: [
    BarberBookCompletePage,
  ],
  imports: [
    IonicPageModule.forChild(BarberBookCompletePage),
    TranslateModule.forChild(),
    BackButtonComponentModule,
    BookingPaypalPaymentComponentModule,
    BookingStripePaymentComponentModule,
    HelpComponentModule
  ],
})
export class BarberBookCompletePageModule {}
