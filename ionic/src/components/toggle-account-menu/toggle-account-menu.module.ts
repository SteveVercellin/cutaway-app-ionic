import { IonicPageModule } from 'ionic-angular';
import { ToggleAccountMenuComponent } from './toggle-account-menu';
import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from "@angular/core";

@NgModule({
    declarations: [
        ToggleAccountMenuComponent
    ],
    imports: [
        IonicPageModule.forChild(ToggleAccountMenuComponent)
    ],
    exports: [
        ToggleAccountMenuComponent
    ],
    schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class ToggleAccountMenuComponentModule{}