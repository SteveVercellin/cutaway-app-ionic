import { Component } from '@angular/core';
import { IonicPage } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';

import { BasePageProvider } from './../../providers/base-page/base-page';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { TranslateProvider } from './../../providers/translate/translate';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { NotifyProvider } from './../../providers/notify/notify';
import { BooklyIntergrateProvider } from './../../providers/bookly-intergrate/bookly-intergrate';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { AuthorizeProvider } from './../../providers/authorize/authorize';

/**
 * Generated class for the BarberSupportPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-barber-support',
  templateUrl: 'barber-support.html',
})
export class BarberSupportPage extends BasePageProvider {

  questions;
  email;
  phone;

  constructor(
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public loadingCtrl: LoadingPopupProvider,
    public nav: NavigationProvider,
    public notify: NotifyProvider,
    public bookly: BooklyIntergrateProvider,
    public parseErr: ParseErrorProvider,
    public libFuncs: LibFunctionsProvider,
    public authorize: AuthorizeProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'staff';
    this.questions = null;
    this.email = 'andrea.audisio@gmail.com';
    this.phone = '0123456789';
  }

  async ionViewDidLoad()
  {
    let question = await this.translateCls.loadTranslateString('QUESTION');
    let answer = await this.translateCls.loadTranslateString('ANSWER');
    let answerContent = '<p>' + answer + ':</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>';

    this.questions = {};
    for (let order = 1; order <= 5; order++) {
      this.questions[order - 1] = {
        'title': question + ' ' + order,
        'content': this.libFuncs.makeHtmlTrusted(answerContent),
        'open': false,
        'index': order - 1
      };
    }
    this.questions = this.libFuncs.convertObjectToArray(this.questions);
  }

}
