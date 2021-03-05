import { Component } from '@angular/core';
import { IonicPage } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';
import { Validators, FormBuilder, FormControl } from '@angular/forms';

import { BasePageProvider } from './../../providers/base-page/base-page';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { TranslateProvider } from './../../providers/translate/translate';
import { NotifyProvider } from './../../providers/notify/notify';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { AuthorizeProvider } from './../../providers/authorize/authorize';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';

import { EmailValidator } from '../../validators/email.validator';

/**
 * Generated class for the ForgetPasswordPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-forget-password',
  templateUrl: 'forget-password.html',
})
export class ForgetPasswordPage extends BasePageProvider {

  constructor(
    public navCtrl: NavigationProvider,
    public authProvider: AuthenticateProvider,
    public notify: NotifyProvider,
    public translateCls: TranslateProvider,
    public formBuilder: FormBuilder,
    public loadingCtrl: LoadingPopupProvider,
    public authorize: AuthorizeProvider,
    public libs: LibFunctionsProvider,
    translate: TranslateService
  ) {
    super(authProvider, navCtrl, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'logout';
    this.authorize.authorize = '';
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['reset_pass_fail'] = 'Reset password failed.';
    this.translateStrings['reset_pass_success'] = 'Reset password was successful.';

    this.validationMessages = {
      'email': [
        { type: 'required', message: 'Email is mandatory field.' },
        { type: 'invalid', message: 'Email is invalid.' }
      ]
    };

    this.translateCls.loadTranslateString('SEND_RESET_PASS_MAIL_FAIL').then(str => {
      this.translateStrings['reset_pass_fail'] = str;
    });
    this.translateCls.loadTranslateString('SEND_RESET_PASS_MAIL_SUCCESS').then(str => {
      this.translateStrings['reset_pass_success'] = str;
    });
    this.translateCls.loadTranslateString('EMAIL_REQUIRED').then(str => {
      this.validationMessages['email'][0]['message'] = str;
    });
    this.translateCls.loadTranslateString('EMAIL_INVALID').then(str => {
      this.validationMessages['email'][1]['message'] = str;
    });

    this.validationsForm = this.formBuilder.group({
      email: new FormControl('', Validators.compose([
        Validators.required,
        EmailValidator.invalid
      ]))
    });
  }

  onResetPass()
  {
    if (this.checkFormCanSubmit()) {
      let resetPassData = this.validationsForm.value;

      this.makePageIsLoading();
      let response = {
        'nonce': 'pass'
      };
      let status = '';

      resetPassData['nonce'] = response['nonce'];
      this.authProvider.sendResetPass(resetPassData).subscribe(response => {
        response = this.libs.parseHttpResponse(response);
        if (response['status'] == 'ok') {
          status = 'ok';
        } else {
          status = 'fail';
        }

        this.processAfterResetPass(status);
      }, err => {
        this.processAfterResetPass('fail');
      });
    }
  }

  processAfterResetPass(status)
  {
    let updated = false;
    let errMsg = '';

    switch (status) {
      case 'ok':
        updated = true;
        break;
      case 'fail':
      default:
        errMsg = this.translateStrings['reset_pass_fail'];
        break;
    }

    this.makePageIsNotLoading();

    if (updated) {
      this.notify.notifyToast(this.translateStrings['reset_pass_success']);
      this.validationsForm.controls.email.setValue('');
    } else {
      this.notify.warningAlert(errMsg);
    }
  }

}
