import { Component } from '@angular/core';
import { Validators, FormBuilder, FormControl } from '@angular/forms';
import { Facebook } from '@ionic-native/facebook';
import { GooglePlus } from '@ionic-native/google-plus';
import { TranslateService } from '@ngx-translate/core';
import { Events, IonicPage } from 'ionic-angular';

import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { NotifyProvider } from './../../providers/notify/notify';
import { DebugProvider } from './../../providers/debug/debug';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { TranslateProvider } from './../../providers/translate/translate';
import { BasePageProvider } from './../../providers/base-page/base-page';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { AuthorizeProvider } from './../../providers/authorize/authorize';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';

@IonicPage()
@Component({
  selector: 'page-home',
  templateUrl: 'home.html'
})
export class HomePage extends BasePageProvider {

  logo;

  constructor(
    public navCtrl: NavigationProvider,
    public authProvider: AuthenticateProvider,
    public fb: Facebook,
    public notify: NotifyProvider,
    public debug: DebugProvider,
    public google: GooglePlus,
    public formBuilder: FormBuilder,
    public translateCls: TranslateProvider,
    public event: Events,
    public loadingCtrl: LoadingPopupProvider,
    public authorize: AuthorizeProvider,
    public libs: LibFunctionsProvider,
    translate: TranslateService
  ) {
    super(authProvider, navCtrl, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'logout';
    this.authorize.authorize = '';

    this.logo = this.libs.getImageFollowOS('home', 'logo');
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['login_fail'] = 'Login failed.';

    this.validationMessages = {
      'username': [
        { type: 'required', message: 'Username is mandatory field.' }
      ],
      'password': [
        { type: 'required', message: 'Password is mandatory field.' }
      ]
    };

    this.translateCls.loadTranslateString('LOGIN_FAIL').then(str => {
      this.translateStrings['login_fail'] = str;
    });
    this.translateCls.loadTranslateString('EMAIL_REQUIRED').then(str => {
      this.validationMessages['username'][0]['message'] = str;
    });
    this.translateCls.loadTranslateString('PASSWORD_REQUIRED').then(str => {
      this.validationMessages['password'][0]['message'] = str;
    });

    this.validationsForm = this.formBuilder.group({
      username: new FormControl('', Validators.compose([
        Validators.required
      ])),
      password: new FormControl('', Validators.required)
    });
  }

  ionViewDidEnter()
  {
    this.changeStatusPushToNewPage(false);
  }

  goToRegisterPage()
  {
    if (!this.checkPushedToNewPage()) {
      this.changeStatusPushToNewPage(true);

      this.navCtrl.pushToPage('RegisterPage');
    }
  }

  goToForgetPassPage()
  {
    if (!this.checkPushedToNewPage()) {
      this.changeStatusPushToNewPage(true);

      this.navCtrl.pushToPage('ForgetPasswordPage');
    }
  }

  onLogin()
  {
    if (this.checkFormCanSubmit()) {
      this.makePageIsLoading();
      this.authProvider.postLogin(this.validationsForm.value).subscribe(response => {
        this.writeAuthenticatedStorage(response);
      }, err => {
        this.processAfterLogin('fail');
      });
    }
  }

  fbLogin()
  {
    if (!this.isLoading) {
      this.makePageIsLoading();
      this.fb.login(['public_profile', 'email']).then(res => {
        if(res.status == 'connected') {
          var fb_id = res.authResponse.userID;
          var accessToken = res.authResponse.accessToken;

          this.fb.api("/" + fb_id + "/?fields=id,email,name", ['public_profile']).then(user => {
            if (user.id != '') {
              let response = {
                'nonce': 'pass'
              };

              this.authProvider.fbPostLogin({
                email: user.email,
                name: user.name,
                id: fb_id,
                access_token: accessToken,
                nonce: response['nonce'],
                social: 'facebook'
              }).subscribe(response => {
                this.writeAuthenticatedStorage(response, 'facebook');
              }, err => {
                this.processAfterLogin('fail');
              });
            } else {
              this.processAfterLogin('fail');
            }
          }, err => {
            this.logSocialAuthenticateFail('facebook', err);
          });
        } else {
          this.processAfterLogin('fail');
        }
      }, err => {
        this.logSocialAuthenticateFail('facebook', err);
      });
    }
  }

  googleLogin()
  {
    if (!this.isLoading) {
      this.makePageIsLoading();
      this.google.login({}).then(user => {
        let response = {
          'nonce': 'pass'
        };

        this.authProvider.googlePostLogin({
          email: user.email,
          name: user.displayName,
          id: user.userId,
          access_token: user.accessToken,
          //id_token: user.idToken,
          nonce: response['nonce'],
          social: 'google'
        }).subscribe(response => {
          this.writeAuthenticatedStorage(response, 'google');
        }, err => {
          this.logSocialAuthenticateFail('google', err);
        });
      }, err => {
        this.logSocialAuthenticateFail('google', err);
      });
    }
  }

  logSocialAuthenticateFail(social: string, error: any, email: string = '')
  {
    let dataLog = {
      social: social,
      email: email,
      error: error
    };
    this.authProvider.logAuthenticateError(dataLog).subscribe(response => {
      this.processAfterLogin('fail');
    }, err => {
      this.processAfterLogin('fail');
    });
  }

  writeAuthenticatedStorage(data, social: string = '')
  {
    data = this.libs.parseHttpResponse(data);

    if (data['status'] == 'ok') {
      data = data['data'];
      if (data.hasOwnProperty("token") && data['token'] != '') {
        if (social != '') {
          data['social_login'] = social;
        }
        this.authProvider.writeAuthenticatedStorage(data).then(() => {
          this.processAfterLogin('ok', data);
        }, err => {
          this.processAfterLogin('fail');
        });
      }
    } else {
      this.processAfterLogin('fail');
    }
  }

  processAfterLogin(status, data: any = {})
  {
    this.makePageIsNotLoading();
    let loginFailMsg = this.translateStrings['login_fail'];

    if (status == 'ok') {
      this.event.publish('logined', data);
      let userType = this.authProvider.determineUserType(data);
      if (userType == 'staff') {
        this.navCtrl.goToPage('BarberDashboardPage');
      } else {
        this.navCtrl.goToPage('FindBarberPage');
      }
    } else {
      this.notify.warningAlert(loginFailMsg);
      this.validationsForm.controls.password.setValue('');
    }
  }

}
