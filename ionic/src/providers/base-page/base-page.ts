import { Injectable } from '@angular/core';
import { FormGroup } from '@angular/forms';

import { AuthMiddlewareProvider } from './../../providers/auth-middleware/auth-middleware';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { NotifyProvider } from './../../providers/notify/notify';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { TranslateProvider } from './../../providers/translate/translate';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { AuthorizeProvider } from './../../providers/authorize/authorize';

/*
  Generated class for the BasePageProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class BasePageProvider extends AuthMiddlewareProvider {

  isLoading: boolean;
  validationsForm: FormGroup;
  validationMessages: any;
  translateStrings: any = {};
  pushedToNewPage;

  constructor(
    public auth: AuthenticateProvider,
    public nav: NavigationProvider,
    public trans: TranslateProvider,
    public loading: LoadingPopupProvider,
    public notify: NotifyProvider,
    public authorize: AuthorizeProvider
  ) {
    super(auth, authorize);

    this.isLoading = false;
    this.pushedToNewPage = false;
    this.validationsForm = null;
  }

  loadData()
  {
    this.translateStrings['authenticate_fail'] = 'Authenticate failed.';
    this.translateStrings['please_wait'] = 'Please wait.';

    if (this.authorize.authenticate == 'login') {
      this.nav.getMenuController().enable(true, 'account-menu');
    }

    this.trans.loadTranslateString('AUTHENTICATE_DATA_FAIL').then(str => {
      this.translateStrings['authenticate_fail'] = str;
    });
    this.trans.loadTranslateString('PLEASE_WAIT').then(str => {
      this.translateStrings['please_wait'] = str;
    });
  }

  clearAuthenticatedStorage()
  {
    let that = this;

    return new Promise((resolve, reject) => {
      this.auth.clearAuthenticatedStorage().then(() => {
        this.makePageIsNotLoading();
        let alertBox = this.notify.warningAlert(this.translateStrings['authenticate_fail']);
        alertBox.onDidDismiss(function (data, role) {
          that.nav.goToPage('HomePage');
        });
        resolve();
      }).catch(err => {
        reject(err)
      });
    });
  }

  checkFormCanSubmit()
  {
    let valid = true;
    valid = valid && !this.isLoading;
    if (this.validationsForm != null) {
      valid = valid && this.validationsForm.valid;
    }

    return valid;
  }

  makePageIsLoading()
  {
    this.isLoading = true;
    this.loading.showLoadingPopup(this.translateStrings['please_wait']);
  }

  makePageIsNotLoading()
  {
    this.isLoading = false;
    this.loading.hideLoadingPopup();
  }

  checkPushedToNewPage()
  {
    return this.pushedToNewPage;
  }

  changeStatusPushToNewPage(status)
  {
    this.pushedToNewPage = status;
  }

}
