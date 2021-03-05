import { BackButtonComponent } from './back-button';
import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from "@angular/core";
import { IonicPageModule } from 'ionic-angular';

@NgModule({
    declarations: [
        BackButtonComponent
    ],
    imports: [
        IonicPageModule.forChild(BackButtonComponent)
    ],
    exports: [
        BackButtonComponent
    ],
    schemas: [
        CUSTOM_ELEMENTS_SCHEMA
    ]
})
export class BackButtonComponentModule{}