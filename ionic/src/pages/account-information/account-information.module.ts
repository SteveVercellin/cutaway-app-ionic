import { ToggleAccountMenuComponentModule } from './../../components/toggle-account-menu/toggle-account-menu.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { AccountInformationPage } from './account-information';

@NgModule({
  declarations: [
    AccountInformationPage,
  ],
  imports: [
    IonicPageModule.forChild(AccountInformationPage),
    TranslateModule.forChild(),
    ToggleAccountMenuComponentModule
  ],
})
export class AccountInformationPageModule {}
