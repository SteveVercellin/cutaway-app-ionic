import { TranslateModule } from '@ngx-translate/core';
import { BookingStripePaymentComponent } from './booking-stripe-payment';
import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from "@angular/core";
import { IonicPageModule } from 'ionic-angular';

@NgModule({
    declarations: [
        BookingStripePaymentComponent
    ],
    imports: [
        TranslateModule,
        IonicPageModule.forChild(BookingStripePaymentComponent)
    ],
    exports: [
        BookingStripePaymentComponent
    ],
    schemas: [
        CUSTOM_ELEMENTS_SCHEMA
    ]
})
export class BookingStripePaymentComponentModule{}