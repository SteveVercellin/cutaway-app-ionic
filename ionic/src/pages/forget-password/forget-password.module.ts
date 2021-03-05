import { BackButtonComponentModule } from './../../components/back-button/back-button.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { ForgetPasswordPage } from './forget-password';

@NgModule({
  declarations: [
    ForgetPasswordPage,
  ],
  imports: [
    IonicPageModule.forChild(ForgetPasswordPage),
    TranslateModule.forChild(),
    BackButtonComponentModule
  ],
})
export class ForgetPasswordPageModule {}
