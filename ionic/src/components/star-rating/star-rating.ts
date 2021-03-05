import { Component, Input } from '@angular/core';

import { TranslateService } from '@ngx-translate/core';
import { AlertController, Events } from 'ionic-angular';

import { TranslateProvider } from './../../providers/translate/translate';

/**
 * Generated class for the StarRatingComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
  selector: 'star-rating',
  templateUrl: 'star-rating.html'
})
export class StarRatingComponent {

  @Input() rating: any;

  totalStars: number;
  inputStars: number;
  inputTitle;
  inputOkLabel;
  inputCancelLabel;

  constructor(
    public alertCtrl: AlertController,
    public translateCls: TranslateProvider,
    public event: Events,
    translate: TranslateService
  ) {
    this.totalStars = 5;
    this.inputStars = 1;

    this.inputTitle = 'Your star rating';
    this.inputOkLabel = 'Ok';
    this.inputCancelLabel = 'Cancel';

    this.translateCls.loadTranslateString('YOUR_STAR_RATING').then(str => {
      this.inputTitle = str;
    });
    this.translateCls.loadTranslateString('OK').then(str => {
      this.inputOkLabel = str;
    });
    this.translateCls.loadTranslateString('CANCEL').then(str => {
      this.inputCancelLabel = str;
    });
  }

  inputStarRating()
  {
    let that = this;

    let alert = that.alertCtrl.create();
    alert.setTitle(that.inputTitle);

    for (let begin = 1; begin <= that.totalStars; begin++) {
      alert.addInput({
        type: 'radio',
        label: begin.toString(),
        value: begin.toString(),
        checked: begin == that.inputStars
      });
    }

    alert.addButton({
      text: that.inputCancelLabel,
      role: 'cancel'
    });
    alert.addButton({
      text: that.inputOkLabel,
      handler: data => {
        that.inputStars = data;
        that.rating.percent = Math.round((that.inputStars / that.totalStars) * 100);

        that.event.publish('star_rating_new_rate', that.inputStars);
      }
    });

    alert.present();
  }

}
