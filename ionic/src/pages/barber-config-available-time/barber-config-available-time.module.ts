import { ToggleAccountMenuComponentModule } from './../../components/toggle-account-menu/toggle-account-menu.module';
import { CalendarComponentModule } from './../../components/calendar/calendar.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { BarberConfigAvailableTimePage } from './barber-config-available-time';

@NgModule({
  declarations: [
    BarberConfigAvailableTimePage,
  ],
  imports: [
    IonicPageModule.forChild(BarberConfigAvailableTimePage),
    TranslateModule.forChild(),
    CalendarComponentModule,
    ToggleAccountMenuComponentModule
  ],
})
export class BarberConfigAvailableTimePageModule {}
