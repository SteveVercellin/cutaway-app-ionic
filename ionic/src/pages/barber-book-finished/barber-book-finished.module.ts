import { ToggleAccountMenuComponentModule } from './../../components/toggle-account-menu/toggle-account-menu.module';
import { HelpComponentModule } from './../../components/help/help.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { BarberBookFinishedPage } from './barber-book-finished';

@NgModule({
  declarations: [
    BarberBookFinishedPage,
  ],
  imports: [
    IonicPageModule.forChild(BarberBookFinishedPage),
    TranslateModule.forChild(),
    HelpComponentModule,
    ToggleAccountMenuComponentModule
  ],
})
export class BarberBookFinishedPageModule {}
