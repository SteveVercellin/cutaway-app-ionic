import { Injectable } from '@angular/core';
import { LoadingController } from 'ionic-angular';

/*
  Generated class for the LoadingPopupProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class LoadingPopupProvider {

  loadingPopup;

  constructor(
    public loadingCtrl: LoadingController
  ) {
    this.loadingPopup = null;
  }

  showLoadingPopup(message: string)
  {
    this.loadingPopup = this.loadingCtrl.create({
      content: message
    });
    this.loadingPopup.present();
  }

  hideLoadingPopup()
  {
    if (this.loadingPopup) {
      this.loadingPopup.dismiss();
    }
  }

}
