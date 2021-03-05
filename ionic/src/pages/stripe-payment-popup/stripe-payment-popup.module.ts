import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { StripePaymentPopupPage } from './stripe-payment-popup';

@NgModule({
  declarations: [
    StripePaymentPopupPage,
  ],
  imports: [
    IonicPageModule.forChild(StripePaymentPopupPage),
    TranslateModule.forChild(),
  ],
})
export class StripePaymentPopupPageModule {}
