import { Component } from '@angular/core';
import { IonicPage } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';
import { Validators, FormBuilder, FormControl } from '@angular/forms';

import { BasePageProvider } from './../../providers/base-page/base-page';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { TranslateProvider } from './../../providers/translate/translate';
import { NotifyProvider } from './../../providers/notify/notify';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { AuthorizeProvider } from './../../providers/authorize/authorize';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';

import { StrengthPasswordValidator } from '../../validators/strength-password.validator';

/**
 * Generated class for the ChangeAccountPasswordPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-change-account-password',
  templateUrl: 'change-account-password.html',
})
export class ChangeAccountPasswordPage extends BasePageProvider {

  passLength: number = 8;
  authenticatedData;

  constructor(
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public formBuilder: FormBuilder,
    public loadingCtrl: LoadingPopupProvider,
    public notify: NotifyProvider,
    public parseErr: ParseErrorProvider,
    public navCtrl: NavigationProvider,
    public authorize: AuthorizeProvider,
    public libs: LibFunctionsProvider,
    translate: TranslateService
  ) {
    super(authProvider, navCtrl, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['change_password_fail'] = 'Change your password failed.';
    this.translateStrings['change_password_success'] = 'Changed your password.';
    this.translateStrings['old_password_not_match'] = 'Your old password did not match.';

    this.validationMessages = {
      'old_password': [
        { type: 'required', message: 'Old password is mandatory field.' }
      ],
      'new_password': [
        { type: 'required', message: 'New password is mandatory field.' },
        { type: 'minlength', message: 'New password is too short.' },
        { type: 'invalid', message: 'New password is too weak.' }
      ]
    };

    this.translateCls.loadTranslateString('CHANGE_PASSWORD_ACCOUNT_FAIL').then(str => {
      this.translateStrings['change_password_fail'] = str;
    });

    this.translateCls.loadTranslateString('CHANGE_PASSWORD_ACCOUNT_SUCCESS').then(str => {
      this.translateStrings['change_password_success'] = str;
    });

    this.translateCls.loadTranslateString('OLD_PASSWORD_NOT_MATCH').then(str => {
      this.translateStrings['old_password_not_match'] = str;
    });

    this.translateCls.loadTranslateString('PASSWORD_REQUIRED').then(str => {
      this.validationMessages['old_password'][0]['message'] = str;
      this.validationMessages['new_password'][0]['message'] = str;
    });

    this.translateCls.loadTranslateString('PASSWORD_LENGTH', {length: this.passLength}).then(str => {
      this.validationMessages['new_password'][1]['message'] = str;
    });

    this.translateCls.loadTranslateString('PASSWORD_STRENGTH').then(str => {
      this.validationMessages['new_password'][2]['message'] = str;
    });

    this.validationsForm = this.formBuilder.group({
      old_password: new FormControl('', Validators.compose([
        Validators.required
      ])),
      new_password: new FormControl('', Validators.compose([
        Validators.required,
        Validators.minLength(this.passLength),
        StrengthPasswordValidator.invalid
      ]))
    });
  }

  onChangePassword()
  {
    if (this.checkFormCanSubmit()) {
      this.makePageIsLoading();
      let status = '';

      this.authProvider.readAuthenticatedStorage().then(data => {
        if (data) {
          this.authenticatedData = data;
          let response = {
            'nonce': 'pass'
          };

          let newUserPasswords = this.validationsForm.value;
          newUserPasswords['nonce'] = response['nonce'];

          this.authProvider.changeUserPassword(newUserPasswords, this.authenticatedData.token).subscribe(response => {
            response = this.libs.parseHttpResponse(response);

            status = response['status'];
            if (status != 'ok') {
              status = response['error'];
            }

            this.processAfterChangePassword(status);
          }, err => {
            if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
              status = 'authenticate_fail';
            } else {
              status = 'fail';
            }

            this.processAfterChangePassword(status);
          });
        } else {
          this.processAfterChangePassword('authenticate_fail');
        }
      }, err => {
        this.processAfterChangePassword('authenticate_fail');
      })
    }
  }

  processAfterChangePassword(status)
  {
    let updated = false;
    let errMsg = '';
    let closePageLoading = true;

    switch (status) {
      case 'ok':
        updated = true;
        break;
      case 'authenticate_fail':
        this.clearAuthenticatedStorage();
        closePageLoading = false;
        break;
      case 'old_pass_not_match':
        errMsg = this.translateStrings['old_password_not_match'];
        break;
      case 'fail':
      default:
        errMsg = this.translateStrings['change_password_fail'];
        break;
    }


    if (closePageLoading) {
      this.makePageIsNotLoading();
    }

    if (updated) {
      this.notify.notifyToast(this.translateStrings['change_password_success']);
      this.validationsForm.controls.old_password.setValue('');
      this.validationsForm.controls.new_password.setValue('');
    } else if (errMsg != '') {
      this.notify.warningAlert(errMsg);
    }
  }

}
