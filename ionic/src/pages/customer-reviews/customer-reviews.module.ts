import { ToggleAccountMenuComponentModule } from './../../components/toggle-account-menu/toggle-account-menu.module';
import { BarberBoxComponentModule } from './../../components/barber-box/barber-box.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { CustomerReviewsPage } from './customer-reviews';

@NgModule({
  declarations: [
    CustomerReviewsPage,
  ],
  imports: [
    IonicPageModule.forChild(CustomerReviewsPage),
    TranslateModule.forChild(),
    BarberBoxComponentModule,
    ToggleAccountMenuComponentModule
  ],
})
export class CustomerReviewsPageModule {}
