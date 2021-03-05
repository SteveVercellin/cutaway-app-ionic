import { ToggleAccountMenuComponentModule } from './../../components/toggle-account-menu/toggle-account-menu.module';
import { BarberBoxComponentModule } from './../../components/barber-box/barber-box.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { BarberReviewsPage } from './barber-reviews';

@NgModule({
  declarations: [
    BarberReviewsPage,
  ],
  imports: [
    IonicPageModule.forChild(BarberReviewsPage),
    TranslateModule.forChild(),
    ToggleAccountMenuComponentModule,
    BarberBoxComponentModule
  ],
})
export class BarberReviewsPageModule {}
