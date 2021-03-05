import { ToggleAccountMenuComponentModule } from './../../components/toggle-account-menu/toggle-account-menu.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { BarberOrdersPage } from './barber-orders';

@NgModule({
  declarations: [
    BarberOrdersPage,
  ],
  imports: [
    IonicPageModule.forChild(BarberOrdersPage),
    TranslateModule.forChild(),
    ToggleAccountMenuComponentModule
  ],
})
export class BarberOrdersPageModule {}
