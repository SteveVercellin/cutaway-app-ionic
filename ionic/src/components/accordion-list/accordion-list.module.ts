import { AccordionListComponent } from './accordion-list';
import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from "@angular/core";
import { IonicPageModule } from 'ionic-angular';

@NgModule({
    declarations: [
        AccordionListComponent
    ],
    imports: [
        IonicPageModule.forChild(AccordionListComponent)
    ],
    exports: [
        AccordionListComponent
    ],
    schemas: [
        CUSTOM_ELEMENTS_SCHEMA
    ]
})
export class AccordionListComponentModule{}