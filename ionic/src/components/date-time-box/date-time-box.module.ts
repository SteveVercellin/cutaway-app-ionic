import { TranslateModule } from '@ngx-translate/core';
import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from "@angular/core";
import { DateTimeBoxComponent } from "./date-time-box";
import { IonicPageModule } from 'ionic-angular';

@NgModule({
    declarations: [
        DateTimeBoxComponent
    ],
    imports: [
        TranslateModule,
        IonicPageModule.forChild(DateTimeBoxComponent)
    ],
    exports: [
        DateTimeBoxComponent
    ],
    schemas: [
        CUSTOM_ELEMENTS_SCHEMA
    ]
})
export class DateTimeBoxComponentModule{}