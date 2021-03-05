import { BackButtonComponentModule } from './../../components/back-button/back-button.module';
import { StarRatingComponentModule } from './../../components/star-rating/star-rating.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { FindBarberResultPage } from './find-barber-result';

@NgModule({
  declarations: [
    FindBarberResultPage,
  ],
  imports: [
    IonicPageModule.forChild(FindBarberResultPage),
    TranslateModule.forChild(),
    StarRatingComponentModule,
    BackButtonComponentModule
  ],
})
export class FindBarberResultPageModule {}
