import { TranslateModule } from '@ngx-translate/core';
import { HelpComponent } from './help';
import { NgModule } from "@angular/core";
import { IonicPageModule } from 'ionic-angular';

@NgModule({
    declarations: [
        HelpComponent
    ],
    imports: [
        IonicPageModule.forChild(HelpComponent),
        TranslateModule
    ],
    exports: [
        HelpComponent
    ]
})
export class HelpComponentModule{}