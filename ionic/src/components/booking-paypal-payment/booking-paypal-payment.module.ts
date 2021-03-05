import { IonicPageModule } from 'ionic-angular';
import { TranslateModule } from '@ngx-translate/core';
import { BookingPaypalPaymentComponent } from './booking-paypal-payment';
import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from "@angular/core";

@NgModule({
    declarations: [
        BookingPaypalPaymentComponent
    ],
    imports: [
        TranslateModule,
        IonicPageModule.forChild(BookingPaypalPaymentComponent)
    ],
    exports: [
        BookingPaypalPaymentComponent
    ],
    schemas: [
        CUSTOM_ELEMENTS_SCHEMA
    ]
})
export class BookingPaypalPaymentComponentModule{}