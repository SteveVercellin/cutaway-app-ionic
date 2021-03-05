import { Injectable } from '@angular/core';
import { AlertController, ToastController } from 'ionic-angular';
import { TranslateProvider } from './../../providers/translate/translate';

/*
  Generated class for the NotifyProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class NotifyProvider {

  translateStrings: any = {};

  constructor(
    public alertCtrl: AlertController,
    public translate: TranslateProvider,
    public toastCtrl: ToastController
  ) {
    this.loadTranslateStrings();
  }

  loadTranslateStrings()
  {
    this.translateStrings['notify'] = 'Notify';
    this.translateStrings['warning'] = 'Warning';
    this.translateStrings['close'] = 'Close';

    this.translate.loadTranslateString('NOTIFY').then(str => {
      this.translateStrings['notify'] = str;
    });
    this.translate.loadTranslateString('WARNING').then(str => {
      this.translateStrings['warning'] = str;
    });
    this.translate.loadTranslateString('CLOSE').then(str => {
      this.translateStrings['close'] = str;
    });
  }

  warningAlert(message: string = '')
  {
    let alert = null;

    if (message != '') {
      alert = this.alertCtrl.create({
        title: this.translateStrings['warning'],
        subTitle: message,
          buttons: [this.translateStrings['close']]
      });
      alert.present();

    }

    return alert;
  }

  notifyAlert(message: string = '')
  {
    let alert = null;

    if (message != '') {
      alert = this.alertCtrl.create({
        title: this.translateStrings['notify'],
        subTitle: message,
        buttons: [this.translateStrings['close']]
      });
      alert.present();
    }

    return alert;
  }

  notifyToast(message: string = '')
  {
    let toast = null;

    if (message != '') {
      toast = this.toastCtrl.create({
        message: message,
        position: 'bottom',
        duration: 3000
      });
      toast.present();
    }

    return toast;
  }

}
