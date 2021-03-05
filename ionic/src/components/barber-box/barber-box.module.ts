import { IonicPageModule } from 'ionic-angular';
import { BarberBoxComponent } from './barber-box';
import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from "@angular/core";

@NgModule({
    declarations: [
        BarberBoxComponent
    ],
    imports: [
        IonicPageModule.forChild(BarberBoxComponent)
    ],
    exports: [
        BarberBoxComponent
    ],
    schemas: [
        CUSTOM_ELEMENTS_SCHEMA
    ]
})
export class BarberBoxComponentModule{}