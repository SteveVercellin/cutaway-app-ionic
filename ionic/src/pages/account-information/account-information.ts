import { Component } from '@angular/core';
import { IonicPage, Events } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';
import { Validators, FormBuilder, FormControl } from '@angular/forms';

import { BasePageProvider } from './../../providers/base-page/base-page';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { TranslateProvider } from './../../providers/translate/translate';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { NotifyProvider } from './../../providers/notify/notify';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { AuthorizeProvider } from './../../providers/authorize/authorize';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';

import { EmailValidator } from '../../validators/email.validator';

/**
 * Generated class for the AccountInformationPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-account-information',
  templateUrl: 'account-information.html',
})
export class AccountInformationPage extends BasePageProvider {

  authenticatedData;
  isSocialUser;

  constructor(
    public navCtrl: NavigationProvider,
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public formBuilder: FormBuilder,
    public loadingCtrl: LoadingPopupProvider,
    public notify: NotifyProvider,
    public parseErr: ParseErrorProvider,
    public event: Events,
    public authorize: AuthorizeProvider,
    public libs: LibFunctionsProvider,
    translate: TranslateService
  ) {
    super(authProvider, navCtrl, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';
    this.isSocialUser = true;
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['edit_acc_infor_fail'] = 'Update account information failed.';
    this.translateStrings['edit_acc_infor_success'] = 'Updated account information.';
    this.translateStrings['edit_acc_infor_email_existed'] = 'Email existed.';

    this.validationMessages = {
      'first_name': [
        { type: 'required', message: 'Name is mandatory field.' }
      ],
      'last_name': [
        { type: 'required', message: 'Last name is mandatory field.' }
      ],
      'email': [
        { type: 'required', message: 'Email is mandatory field.' },
        { type: 'invalid', message: 'Invalid email.' }
      ]
    };

    this.translateCls.loadTranslateString('EDIT_ACCOUNT_INFORMATION_FAIL').then(str => {
      this.translateStrings['edit_acc_infor_fail'] = str;
    });
    this.translateCls.loadTranslateString('EDIT_ACCOUNT_INFORMATION_SUCCESS').then(str => {
      this.translateStrings['edit_acc_infor_success'] = str;
    });
    this.translateCls.loadTranslateString('EMAIL_EXISTED').then(str => {
      this.translateStrings['edit_acc_infor_email_existed'] = str;
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

    this.validationsForm = this.formBuilder.group({
      first_name: new FormControl('', Validators.required),
      last_name: new FormControl('', Validators.required),
      email: new FormControl('', Validators.compose([
        Validators.required,
        EmailValidator.invalid
      ])),
      phone_number: new FormControl('', Validators.compose([])),
      city: new FormControl('', Validators.compose([]))
    });
  }

  ionViewDidEnter()
  {
    this.changeStatusPushToNewPage(false);
    this.setupData(null);
  }

  setupData(event)
  {
    this.makePageIsLoading();
    this.authProvider.readAuthenticatedStorage().then(data => {
      if (data) {
        this.authenticatedData = data;

        let response = {
          'nonce': 'pass'
        };
        this.auth.getUserInformation(response['nonce'], this.authenticatedData.token).subscribe(response => {
          response = this.libs.parseHttpResponse(response);
          let status = response['status'];
          if (status == 'ok') {
            let dataUserGet = response['data'];

            if (dataUserGet.hasOwnProperty("email") && dataUserGet['email'] != '') {
              if (event != null) {
                event.complete();
              }

              this.validationsForm.controls.first_name.setValue(dataUserGet['first_name']);
              this.validationsForm.controls.last_name.setValue(dataUserGet['last_name']);
              this.validationsForm.controls.email.setValue(dataUserGet['email']);
              this.validationsForm.controls.phone_number.setValue(dataUserGet['phone']);
              this.validationsForm.controls.city.setValue(dataUserGet['city']);
              this.isSocialUser = dataUserGet['is_social_user'];
              this.makePageIsNotLoading();
            } else {
              this.setupDataFailed(event);
            }
          } else {
            this.setupDataFailed(event);
          }
        }, err => {
          this.setupDataFailed(event);
        });
      } else {
        this.setupDataFailed(event);
      }
    }).catch(err => {
      this.setupDataFailed(event);
    })
  }

  setupDataFailed(event)
  {
    if (event != null) {
      event.complete();
    }
    this.clearAuthenticatedStorage().then(() => {}, err => {});
  }

  updateAuthenticatedStorage(newData)
  {
    return new Promise((resolve, reject) => {
      this.authenticatedData.user_email = newData.user_email;
      this.authenticatedData.user_nicename = newData.user_nicename;
      this.authenticatedData.user_display_name = newData.user_display_name;
      this.authProvider.writeAuthenticatedStorage(this.authenticatedData).then(() => {
        this.event.publish('logined', this.authenticatedData);
        resolve();
      }, err => {
        reject(err);
      });
    });
  }

  editAccountInformation()
  {
    if (this.checkFormCanSubmit()) {
      this.makePageIsLoading();

      let response = {
        'nonce': 'pass'
      };
      let newUserInformation = this.validationsForm.value;
      newUserInformation['nonce'] = response['nonce'];
      this.authProvider.updateUserInformation(newUserInformation, this.authenticatedData.token).subscribe(response => {
        let updated = false;
        let error = '';
        let dataUserUpdated = {};

        response = this.libs.parseHttpResponse(response);

        let status = response['status'];
        if (status == 'ok') {
          dataUserUpdated = response['data'];
          if (dataUserUpdated.hasOwnProperty("user_email") && dataUserUpdated['user_email'] != '') {
            updated = true;
          }
        } else {
          error = response.hasOwnProperty("error") ? response['error'] : 'fail';
        }

        if (updated) {
          this.updateAuthenticatedStorage(dataUserUpdated).then(() => {
            this.afterEditAccountInformation('success');
          }, err => {
            this.setupDataFailed(null);
          });
        } else {
          this.afterEditAccountInformation(error);
        }
      }, err => {
        let error = this.parseErr.parseHttpErrorResponse(err);
        this.afterEditAccountInformation(error);
      });
    }
  }

  afterEditAccountInformation(result: string)
  {
    let that = this;

    let updateAccountInformationSuccessMsg = that.translateStrings['edit_acc_infor_success'];
    let updateAccountInformationFailMsg = that.translateStrings['edit_acc_infor_fail'];
    let updateAccountInformationFailEmailExistedMsg = that.translateStrings['edit_acc_infor_email_existed'];

    that.makePageIsNotLoading();
    switch (result) {
      case 'email_existed':
        that.notify.warningAlert(updateAccountInformationFailEmailExistedMsg);
        break;
      case 'success':
        that.notify.notifyToast(updateAccountInformationSuccessMsg);
        break;
      case 'fail':
      default:
        that.notify.warningAlert(updateAccountInformationFailMsg);
    }
  }

  goToChangeAccountPasswordPage()
  {
    if (!this.checkPushedToNewPage()) {
      this.changeStatusPushToNewPage(true);

      this.navCtrl.pushToPage('ChangeAccountPasswordPage');
    }

    return false;
  }

}
