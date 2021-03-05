import { TranslateModule } from '@ngx-translate/core';
import { StarRatingComponent } from './star-rating';
import { NgModule, CUSTOM_ELEMENTS_SCHEMA } from "@angular/core";
import { IonicPageModule } from 'ionic-angular';

@NgModule({
    declarations: [
        StarRatingComponent
    ],
    imports: [
        TranslateModule,
        IonicPageModule.forChild(StarRatingComponent)
    ],
    exports: [
        StarRatingComponent
    ],
    schemas: [
        CUSTOM_ELEMENTS_SCHEMA
    ]
})
export class StarRatingComponentModule{}