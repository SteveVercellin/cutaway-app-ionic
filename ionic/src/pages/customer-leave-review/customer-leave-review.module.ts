import { StarRatingComponentModule } from './../../components/star-rating/star-rating.module';
import { ServiceBoxComponentModule } from './../../components/service-box/service-box.module';
import { CalendarComponentModule } from './../../components/calendar/calendar.module';
import { BarberBoxComponentModule } from './../../components/barber-box/barber-box.module';
import { BackButtonComponentModule } from './../../components/back-button/back-button.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { CustomerLeaveReviewPage } from './customer-leave-review';

@NgModule({
  declarations: [
    CustomerLeaveReviewPage,
  ],
  imports: [
    IonicPageModule.forChild(CustomerLeaveReviewPage),
    TranslateModule.forChild(),
    BackButtonComponentModule,
    BarberBoxComponentModule,
    CalendarComponentModule,
    ServiceBoxComponentModule,
    StarRatingComponentModule
  ],
})
export class CustomerLeaveReviewPageModule {}
