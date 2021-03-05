import { ToggleAccountMenuComponentModule } from './../../components/toggle-account-menu/toggle-account-menu.module';
import { AccordionListComponentModule } from './../../components/accordion-list/accordion-list.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { BarberSupportPage } from './barber-support';

@NgModule({
  declarations: [
    BarberSupportPage,
  ],
  imports: [
    IonicPageModule.forChild(BarberSupportPage),
    TranslateModule.forChild(),
    AccordionListComponentModule,
    ToggleAccountMenuComponentModule
  ],
})
export class BarberSupportPageModule {}
