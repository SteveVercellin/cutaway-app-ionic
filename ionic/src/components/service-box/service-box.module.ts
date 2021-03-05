import { TranslateModule } from '@ngx-translate/core';
import { ServiceBoxComponent } from './service-box';
import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from "@angular/core";
import { IonicPageModule } from 'ionic-angular';

@NgModule({
    declarations: [
        ServiceBoxComponent
    ],
    imports: [
        TranslateModule,
        IonicPageModule.forChild(ServiceBoxComponent)
    ],
    exports: [
        ServiceBoxComponent
    ],
    schemas: [
        CUSTOM_ELEMENTS_SCHEMA
    ]
})
export class ServiceBoxComponentModule{}