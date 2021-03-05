import { ToggleAccountMenuComponentModule } from './../../components/toggle-account-menu/toggle-account-menu.module';
import { PaginateComponentModule } from './../../components/paginate/paginate.module';
import { BarberBoxComponentModule } from './../../components/barber-box/barber-box.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { CustomerChronologyPage } from './customer-chronology';

@NgModule({
  declarations: [
    CustomerChronologyPage,
  ],
  imports: [
    IonicPageModule.forChild(CustomerChronologyPage),
    TranslateModule.forChild(),
    BarberBoxComponentModule,
    PaginateComponentModule,
    ToggleAccountMenuComponentModule
  ],
})
export class CustomerChronologyPageModule {}
