import { ServiceBoxComponentModule } from './../../components/service-box/service-box.module';
import { DateTimeBoxComponentModule } from './../../components/date-time-box/date-time-box.module';
import { BarberBoxComponentModule } from './../../components/barber-box/barber-box.module';
import { BackButtonComponentModule } from './../../components/back-button/back-button.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { BarberBookSummaryPage } from './barber-book-summary';

@NgModule({
  declarations: [
    BarberBookSummaryPage,
  ],
  imports: [
    IonicPageModule.forChild(BarberBookSummaryPage),
    TranslateModule.forChild(),
    BackButtonComponentModule,
    BarberBoxComponentModule,
    DateTimeBoxComponentModule,
    ServiceBoxComponentModule
  ],
})
export class BarberBookSummaryPageModule {}
