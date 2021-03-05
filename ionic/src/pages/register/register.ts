import { Component } from '@angular/core';
import { IonicPage, Events } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';
import { Validators, FormBuilder, FormGroup, FormControl } from '@angular/forms';

import { NavigationProvider } from './../../providers/navigation/navigation';
import { BasePageProvider } from './../../providers/base-page/base-page';
import { TranslateProvider } from './../../providers/translate/translate';
import { NotifyProvider } from './../../providers/notify/notify';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { AuthorizeProvider } from './../../providers/authorize/authorize';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';

import { MatchPasswordValidator } from '../../validators/match-password.validator';
import { StrengthPasswordValidator } from '../../validators/strength-password.validator';
import { EmailValidator } from '../../validators/email.validator';

/**
 * Generated class for the RegisterPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-register',
  templateUrl: 'register.html',
})
export class RegisterPage extends BasePageProvider {

  matchingPasswordsGroup: FormGroup;

  passLength: number = 8;

  constructor(
    public navCtrl: NavigationProvider,
    public authProvider: AuthenticateProvider,
    public notify: NotifyProvider,
    public translateCls: TranslateProvider,
    public formBuilder: FormBuilder,
    public parseErr: ParseErrorProvider,
    public loadingCtrl: LoadingPopupProvider,
    public event: Events,
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

    this.translateStrings['register_fail'] = 'Register account failed.';
    this.translateStrings['register_success'] = 'Register account was successful.';
    this.translateStrings['email_existed'] = 'Email existed.';

    this.validationMessages = {
      'first_name': [
        { type: 'required', message: 'Name is mandatory field.' }
      ],
      'last_name': [
        { type: 'required', message: 'Last name is mandatory field.' }
      ],
      'email': [
        { type: 'required', message: 'Email is mandatory field.' },
        { type: 'invalid', message: 'Email is invalid.' }
      ],
      'password': [
        { type: 'required', message: 'Password is mandatory field.' },
        { type: 'minlength', message: 'Password is too short.' },
        { type: 'invalid', message: 'Password is too weak.' }
      ],
      'confirm_password': [
        { type: 'required', message: 'Confirm password is mandatory field.' }
      ],
      'matching_passwords': [
        { type: 'areEqual', message: 'Password and confirm password do not match.' }
      ]
    };

    this.translateCls.loadTranslateString('REGISTER_FAIL').then(str => {
      this.translateStrings['register_fail'] = str;
    });
    this.translateCls.loadTranslateString('REGISTER_SUCCESS').then(str => {
      this.translateStrings['register_success'] = str;
    });
    this.translateCls.loadTranslateString('EMAIL_EXISTED').then(str => {
      this.translateStrings['email_existed'] = str;
    });
    this.translateCls.loadTranslateString('NAME_REQUIRED').then(str => {
      this.validationMessages['first_name'][0]['message'] = str;
    });
    this.translateCls.loadTranslateString('LAST_NAME_REQUIRED').then(str => {
      this.validationMessages['last_name'][0]['message'] = str;
    });
    this.translateCls.loadTranslateString('EMAIL_REQUIRED').then(str => {
      this.validationMessages['email'][0]['message'] = str;
    });
    this.translateCls.loadTranslateString('EMAIL_INVALID').then(str => {
      this.validationMessages['email'][1]['message'] = str;
    });
    this.translateCls.loadTranslateString('PASSWORD_REQUIRED').then(str => {
      this.validationMessages['password'][0]['message'] = str;
    });
    this.translateCls.loadTranslateString('PASSWORD_LENGTH', {length: this.passLength}).then(str => {
      this.validationMessages['password'][1]['message'] = str;
    });
    this.translateCls.loadTranslateString('PASSWORD_STRENGTH').then(str => {
      this.validationMessages['password'][2]['message'] = str;
    });
    this.translateCls.loadTranslateString('CONFIRM_PASSWORD_REQUIRED').then(str => {
      this.validationMessages['confirm_password'][0]['message'] = str;
    });
    this.translateCls.loadTranslateString('PASSWORD_MIS_MATCH').then(str => {
      this.validationMessages['matching_passwords'][0]['message'] = str;
    });

    this.matchingPasswordsGroup = new FormGroup({
      password: new FormControl('', Validators.compose([
        Validators.required,
        Validators.minLength(this.passLength),
        StrengthPasswordValidator.invalid
      ])),
      confirm_password: new FormControl('', Validators.required)
    }, (formGroup: FormGroup) => {
      return MatchPasswordValidator.areEqual(formGroup);
    });

    this.validationsForm = this.formBuilder.group({
      first_name: new FormControl('', Validators.required),
      last_name: new FormControl('', Validators.required),
      email: new FormControl('', Validators.compose([
        Validators.required,
        EmailValidator.invalid
      ])),
      phone_number: new FormControl('', Validators.compose([])),
      city: new FormControl('', Validators.compose([])),
      matching_passwords: this.matchingPasswordsGroup
    });
  }

  onRegister()
  {
    if (this.checkFormCanSubmit()) {
      let newUserData = this.validationsForm.value;
      newUserData['password'] = newUserData['matching_passwords']['password'];
      this.makePageIsLoading();

      let response = {
        'nonce': 'pass'
      };
      newUserData['nonce'] = response['nonce'];
      let status = '';

      this.authProvider.postRegister(newUserData).subscribe(response => {
        response = this.libs.parseHttpResponse(response);
        status = response['status'];
        if (status == 'ok') {
          let dataUserCreated = response['data'];

          if (dataUserCreated.hasOwnProperty("token") && dataUserCreated['token'] != '') {
            this.authProvider.writeAuthenticatedStorage(dataUserCreated).then(() => {
              this.event.publish('logined', dataUserCreated);
              this.processAfterRegister('ok');
            }, err => {
              this.processAfterRegister('fail');
            });
          } else {
            this.processAfterRegister('fail');
          }
        } else {
          let errorMsg = response['error'];
          this.processAfterRegister(errorMsg);
        }
      }, err => {
        let errorMsg = this.parseErr.parseHttpErrorResponse(err);
        status = errorMsg == 'email_existed' ? errorMsg : 'fail';

        this.processAfterRegister(status);
      });
    }
  }

  processAfterRegister(status)
  {
    let registered = false;
    let errMsg = '';

    switch (status) {
      case 'ok':
        registered = true;
        break;
      case 'email_existed':
        errMsg = this.translateStrings['email_existed'];
        break;
      case 'fail':
      default:
        errMsg = this.translateStrings['register_fail'];
        break;
    }

    this.makePageIsNotLoading();

    if (registered) {
      this.navCtrl.goToPage('FindBarberPage');
    } else {
      this.notify.warningAlert(errMsg);
    }
  }

}
