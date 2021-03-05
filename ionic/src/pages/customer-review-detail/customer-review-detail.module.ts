import { HelpComponentModule } from './../../components/help/help.module';
import { BackButtonComponentModule } from './../../components/back-button/back-button.module';
import { TranslateModule } from '@ngx-translate/core';
import { NgModule } from '@angular/core';
import { IonicPageModule } from 'ionic-angular';
import { CustomerReviewDetailPage } from './customer-review-detail';

@NgModule({
  declarations: [
    CustomerReviewDetailPage,
  ],
  imports: [
    IonicPageModule.forChild(CustomerReviewDetailPage),
    TranslateModule.forChild(),
    BackButtonComponentModule,
    HelpComponentModule
  ],
})
export class CustomerReviewDetailPageModule {}
