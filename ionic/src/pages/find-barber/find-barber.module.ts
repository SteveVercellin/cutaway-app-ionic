import { ToggleAccountMenuComponentModule } from './../../components/toggle-account-menu/toggle-account-menu.module';
import { CalendarComponentModule } from './../../components/calendar/calendar.module';
import { IonicSelectableModule } from 'ionic-selectable';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { FindBarberPage } from './find-barber';

@NgModule({
  declarations: [
    FindBarberPage,
  ],
  imports: [
    IonicPageModule.forChild(FindBarberPage),
    TranslateModule.forChild(),
    IonicSelectableModule,
    CalendarComponentModule,
    ToggleAccountMenuComponentModule
  ]
})
export class FindBarberPageModule {}
