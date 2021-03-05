import { BackButtonComponentModule } from './../../components/back-button/back-button.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { ListServicesPage } from './list-services';

@NgModule({
  declarations: [
    ListServicesPage,
  ],
  imports: [
    IonicPageModule.forChild(ListServicesPage),
    TranslateModule.forChild(),
    BackButtonComponentModule
  ],
})
export class ListServicesPageModule {}
