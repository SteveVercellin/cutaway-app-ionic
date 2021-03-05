import { ToggleAccountMenuComponentModule } from './../../components/toggle-account-menu/toggle-account-menu.module';
import { BarberBoxComponentModule } from './../../components/barber-box/barber-box.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { CustomerFavouriteBarbersPage } from './customer-favourite-barbers';

@NgModule({
  declarations: [
    CustomerFavouriteBarbersPage,
  ],
  imports: [
    IonicPageModule.forChild(CustomerFavouriteBarbersPage),
    TranslateModule.forChild(),
    BarberBoxComponentModule,
    ToggleAccountMenuComponentModule
  ],
})
export class CustomerFavouriteBarbersPageModule {}
