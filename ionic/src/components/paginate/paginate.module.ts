import { PaginateComponent } from './paginate';
import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from "@angular/core";
import { IonicPageModule } from 'ionic-angular';

@NgModule({
    declarations: [
        PaginateComponent
    ],
    imports: [
        IonicPageModule.forChild(PaginateComponent)
    ],
    exports: [
        PaginateComponent
    ],
    schemas: [
        CUSTOM_ELEMENTS_SCHEMA
    ]
})
export class PaginateComponentModule{}