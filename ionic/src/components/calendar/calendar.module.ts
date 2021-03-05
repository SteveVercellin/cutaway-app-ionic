import { IonicPageModule } from 'ionic-angular';
import { TranslateModule } from '@ngx-translate/core';
import { CalendarComponent } from './calendar';
import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from "@angular/core";

@NgModule({
    declarations: [
        CalendarComponent
    ],
    imports: [
        TranslateModule,
        IonicPageModule.forChild(CalendarComponent)
    ],
    exports: [
        CalendarComponent
    ],
    schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class CalendarComponentModule{}