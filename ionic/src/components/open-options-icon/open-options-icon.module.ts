import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from "@angular/core";
import { OpenOptionsIconComponent } from "./open-options-icon";
import { IonicPageModule } from "ionic-angular";

@NgModule({
    declarations: [
        OpenOptionsIconComponent
    ],
    imports: [
        IonicPageModule.forChild(OpenOptionsIconComponent)
    ],
    exports: [
        OpenOptionsIconComponent
    ],
    schemas: [
        CUSTOM_ELEMENTS_SCHEMA
    ]
})
export class OpenOptionsIconComponentModule{}