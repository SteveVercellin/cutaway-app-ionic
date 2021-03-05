import { StarRatingComponentModule } from './../../components/star-rating/star-rating.module';
import { ToggleAccountMenuComponentModule } from './../../components/toggle-account-menu/toggle-account-menu.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { BarberDashboardPage } from './barber-dashboard';

@NgModule({
  declarations: [
    BarberDashboardPage
  ],
  imports: [
    IonicPageModule.forChild(BarberDashboardPage),
    TranslateModule.forChild(),
    ToggleAccountMenuComponentModule,
    StarRatingComponentModule
  ]
})
export class BarberDashboardPageModule {}
