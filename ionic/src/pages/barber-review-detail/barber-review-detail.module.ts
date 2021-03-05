import { HelpComponentModule } from '../../components/help/help.module';
import { BackButtonComponentModule } from '../../components/back-button/back-button.module';
import { StarRatingComponentModule } from './../../components/star-rating/star-rating.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { BarberReviewDetailPage } from './barber-review-detail';

@NgModule({
  declarations: [
    BarberReviewDetailPage,
  ],
  imports: [
    IonicPageModule.forChild(BarberReviewDetailPage),
    TranslateModule.forChild(),
    BackButtonComponentModule,
    HelpComponentModule,
    StarRatingComponentModule
  ],
})
export class BarberReviewDetailPageModule {}
