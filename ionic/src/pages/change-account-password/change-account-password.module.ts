import { BackButtonComponentModule } from './../../components/back-button/back-button.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { ChangeAccountPasswordPage } from './change-account-password';

@NgModule({
  declarations: [
    ChangeAccountPasswordPage,
  ],
  imports: [
    IonicPageModule.forChild(ChangeAccountPasswordPage),
    TranslateModule.forChild(),
    BackButtonComponentModule
  ],
})
export class ChangeAccountPasswordPageModule {}
