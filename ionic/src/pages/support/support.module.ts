import { ToggleAccountMenuComponentModule } from './../../components/toggle-account-menu/toggle-account-menu.module';
import { AccordionListComponentModule } from './../../components/accordion-list/accordion-list.module';
import { BackButtonComponentModule } from './../../components/back-button/back-button.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { SupportPage } from './support';

@NgModule({
  declarations: [
    SupportPage,
  ],
  imports: [
    IonicPageModule.forChild(SupportPage),
    TranslateModule.forChild(),
    AccordionListComponentModule,
    ToggleAccountMenuComponentModule,
    BackButtonComponentModule
  ],
})
export class SupportPageModule {}
