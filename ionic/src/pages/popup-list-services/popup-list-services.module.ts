import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { PopupListServicesPage } from './popup-list-services';

@NgModule({
  declarations: [
    PopupListServicesPage,
  ],
  imports: [
    IonicPageModule.forChild(PopupListServicesPage),
    TranslateModule.forChild()
  ],
})
export class PopupListServicesPageModule {}
