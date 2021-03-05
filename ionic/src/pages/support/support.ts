import { Component } from '@angular/core';
import { IonicPage, NavParams } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';

import { BasePageProvider } from './../../providers/base-page/base-page';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { TranslateProvider } from './../../providers/translate/translate';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';
import { BooklyIntergrateProvider } from './../../providers/bookly-intergrate/bookly-intergrate';
import { NotifyProvider } from './../../providers/notify/notify';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { AuthorizeProvider } from './../../providers/authorize/authorize';

import { SupportConfig } from './../../config/support';

/**
 * Generated class for the SupportPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-support',
  templateUrl: 'support.html',
})
export class SupportPage extends BasePageProvider {

  questions;
  openPageType;
  support;

  constructor(
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public nav: NavigationProvider,
    public libFuncs: LibFunctionsProvider,
    public bookly: BooklyIntergrateProvider,
    public notify: NotifyProvider,
    public loadingCtrl: LoadingPopupProvider,
    public authorize: AuthorizeProvider,
    public navParams: NavParams,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';
    this.questions = null;
    this.support = SupportConfig;

    this.openPageType = 'go';
    let dataPassed = this.navParams.data;
    if (dataPassed && dataPassed.hasOwnProperty('open_page_type')) {
      if (dataPassed['open_page_type'] == 'go' || dataPassed['open_page_type'] == 'push') {
        this.openPageType = dataPassed['open_page_type'];
      }
    }
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
