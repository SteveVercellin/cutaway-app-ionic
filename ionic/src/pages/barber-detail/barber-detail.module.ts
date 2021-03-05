import { StarRatingComponentModule } from './../../components/star-rating/star-rating.module';
import { BackButtonComponentModule } from './../../components/back-button/back-button.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { BarberDetailPage } from './barber-detail';

@NgModule({
  declarations: [
    BarberDetailPage,
  ],
  imports: [
    IonicPageModule.forChild(BarberDetailPage),
    TranslateModule.forChild(),
    BackButtonComponentModule,
    StarRatingComponentModule
  ],
})
export class BarberDetailPageModule {}
