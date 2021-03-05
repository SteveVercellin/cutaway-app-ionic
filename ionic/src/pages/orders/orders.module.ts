import { ToggleAccountMenuComponentModule } from './../../components/toggle-account-menu/toggle-account-menu.module';
import { ServiceBoxComponentModule } from './../../components/service-box/service-box.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { OrdersPage } from './orders';

@NgModule({
  declarations: [
    OrdersPage,
  ],
  imports: [
    IonicPageModule.forChild(OrdersPage),
    TranslateModule.forChild(),
    ServiceBoxComponentModule,
    ToggleAccountMenuComponentModule
  ],
})
export class OrdersPageModule {}
