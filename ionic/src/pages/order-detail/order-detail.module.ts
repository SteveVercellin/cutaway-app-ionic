import { ServiceBoxComponentModule } from './../../components/service-box/service-box.module';
import { DateTimeBoxComponentModule } from './../../components/date-time-box/date-time-box.module';
import { BookingPaypalPaymentComponentModule } from './../../components/booking-paypal-payment/booking-paypal-payment.module';
import { BookingStripePaymentComponentModule } from './../../components/booking-stripe-payment/booking-stripe-payment.module';
import { BarberBoxComponentModule } from './../../components/barber-box/barber-box.module';
import { BackButtonComponentModule } from './../../components/back-button/back-button.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { OrderDetailPage } from './order-detail';

@NgModule({
  declarations: [
    OrderDetailPage,
  ],
  imports: [
    IonicPageModule.forChild(OrderDetailPage),
    TranslateModule.forChild(),
    BackButtonComponentModule,
    BarberBoxComponentModule,
    BookingPaypalPaymentComponentModule,
    BookingStripePaymentComponentModule,
    DateTimeBoxComponentModule,
    ServiceBoxComponentModule
  ],
})
export class OrderDetailPageModule {}
