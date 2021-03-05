import { Component, ViewChild } from '@angular/core';
import { Nav, Platform } from 'ionic-angular';
import { StatusBar } from '@ionic-native/status-bar';
import { SplashScreen } from '@ionic-native/splash-screen';
import { ScreenOrientation } from '@ionic-native/screen-orientation';
import { TranslateService } from '@ngx-translate/core';
import { MenuController, Events } from 'ionic-angular';
import { CacheService } from "ionic-cache";
import { NavigationProvider } from './../providers/navigation/navigation';
import { AuthenticateProvider } from './../providers/authenticate/authenticate';
import { TranslateProvider } from './../providers/translate/translate';
import { NotifyProvider } from './../providers/notify/notify';
import { PlafformProvider } from './../providers/plafform/plafform';

import { Facebook } from '@ionic-native/facebook';
import { GooglePlus } from '@ionic-native/google-plus';

@Component({
  templateUrl: 'app.html'
})
export class MyApp {
  @ViewChild(Nav) nav: Nav;
  rootPage: any;
  authenticatedData: any = null;
  authenticatedMenuPages: any[] = [];
  authenticatedCustomerMenuPages: any[] = [
    {
      'title': 'HOME',
      'icon': '../assets/imgs/global/customer-left-menu/home.svg',
      'page': 'FindBarberPage'
    },
    {
      'title': 'ACCOUNT',
      'icon': '../assets/imgs/global/customer-left-menu/person-ico.svg',
      'page': 'AccountInformationPage'
    },
    {
      'title': 'ORDER',
      'icon': '../assets/imgs/global/customer-left-menu/order-ico.svg',
      'page': 'OrdersPage'
    },
    {
      'title': 'CHRONOLOGY',
      'icon': '../assets/imgs/global/customer-left-menu/chronology-ico.svg',
      'page': 'CustomerChronologyPage'
    },
    {
      'title': 'FAVOURITE',
      'icon': '../assets/imgs/global/customer-left-menu/favorites-ico.svg',
      'page': 'CustomerFavouriteBarbersPage'
    },
    {
      'title': 'LEAVE_REVIEW',
      'icon': '../assets/imgs/global/customer-left-menu/leave-review-ico.svg',
      'page': 'CustomerReviewsPage'
    },
    {
      'title': 'SUPPORT',
      'icon': '../assets/imgs/global/customer-left-menu/support-ico.svg',
      'page': 'SupportPage'
    }
  ];
  authenticatedBarberMenuPages: any[] = [
    {
      'title': 'HOME',
      'icon': '../assets/imgs/global/customer-left-menu/home.svg',
      'page': 'BarberDashboardPage'
    },
    {
      'title': 'CALENDAR',
      'icon': '../assets/imgs/global/customer-left-menu/chronology-ico.svg',
      'page': 'BarberConfigAvailableTimePage'
    },
    {
      'title': 'ORDER',
      'icon': '../assets/imgs/global/customer-left-menu/order-ico.svg',
      'page': 'BarberOrdersPage'
    },
    {
      'title': 'REVIEWS',
      'icon': '../assets/imgs/global/customer-left-menu/leave-review-ico.svg',
      'page': 'BarberReviewsPage'
    },
    {
      'title': 'SUPPORT',
      'icon': '../assets/imgs/global/customer-left-menu/support-ico.svg',
      'page': 'BarberSupportPage'
    }
  ];

  constructor(
    platform: Platform,
    statusBar: StatusBar,
    splashScreen: SplashScreen,
    cache: CacheService,
    public navObj: NavigationProvider,
    public screenRotate: ScreenOrientation,
    public auth: AuthenticateProvider,
    public translate: TranslateService,
    public fb: Facebook,
    public google: GooglePlus,
    public translateCls: TranslateProvider,
    public notify: NotifyProvider,
    public menu: MenuController,
    public event: Events,
    public platformProvider: PlafformProvider
  ) {
    let that = this;

    that.translate.setDefaultLang('it');

    platform.ready().then(() => {
      if (this.platformProvider.determinePlatform() == 'app') {
        that.screenRotate.lock(that.screenRotate.ORIENTATIONS.PORTRAIT);
      }
      that.event.unsubscribe('logined');
      that.event.subscribe('logined', (authenticatedData) => {
        that.authenticatedData = authenticatedData;
        that.setupLeftMenu();
      });
      cache.setDefaultTTL(60 * 60);

      // Okay, so the platform is ready and our plugins are available.
      // Here you can do any higher level native things you might need.
      statusBar.styleDefault();
      splashScreen.hide();

      that.auth.readAuthenticatedStorage().then(data => {
        if (data) {
          that.authenticatedData = data;
          that.setupLeftMenu(true);
        } else {
          that.rootPage = 'HomePage';
        }
      }, err => {
        that.rootPage = 'HomePage';
      });
    });
  }

  goToPage(page)
  {
    this.menu.close().then(() => {
      this.navObj.goToPage(page);
    });
  }

  logout()
  {
    let that = this;

    that.menu.close().then(() => {
      that.auth.readAuthenticatedStorage().then(function (data) {
        if (data && data.hasOwnProperty('social_login')) {
          switch (data.social_login) {
            case 'facebook':
              that.fbLogout();
              break;
            case 'google':
              that.googleLogout();
              break;
            default:
              that.clearAuthenticatedStorage();
          }
        } else {
          that.clearAuthenticatedStorage();
        }
      });
    });
  }

  fbLogout()
  {
    let that = this;

    that.fb.getLoginStatus()
    .then(function(response) {
      if (response.status === 'connected') {
        that.fb.logout().then(function(response) {
          that.clearAuthenticatedStorage();
        }, function(error){
          that.clearAuthenticatedStorage();
        });
      } else {
        that.clearAuthenticatedStorage();
      }
    }, function(error){
      that.clearAuthenticatedStorage();
    });
  }

  googleLogout()
  {
    let that = this;

    that.google.disconnect()
    .then(function() {
      that.clearAuthenticatedStorage();
    }, function(error){
      that.clearAuthenticatedStorage();
    });
  }

  clearAuthenticatedStorage()
  {
    this.translateCls.loadTranslateString('LOGOUT_FAIL').then(str => {
      this.menu.close().then(() => {
        this.auth.clearAuthenticatedStorage().then(() => {
          this.authenticatedData = null;
          this.navObj.goToPage('HomePage');
        }).catch(err => {
          this.notify.warningAlert(str);
        })
      }).catch(err => {
        this.notify.warningAlert(str);
      })
    }).catch(err => {
      this.notify.warningAlert('Logout fail');
    })
  }

  setupLeftMenu(jumpToPage: boolean = false)
  {
    let that = this;

    let userType = that.auth.determineUserType(that.authenticatedData);

    if (userType == 'staff') {
      that.authenticatedMenuPages = that.authenticatedBarberMenuPages;
      if (jumpToPage) {
        that.navObj.goToPage('BarberDashboardPage');
      }
    } else {
      that.authenticatedMenuPages = that.authenticatedCustomerMenuPages;
      if (jumpToPage) {
        that.navObj.goToPage('FindBarberPage');
      }
    }

    for (let index = 0; index < that.authenticatedMenuPages.length; index++) {
      that.translate.get(that.authenticatedMenuPages[index]['title']).subscribe(string => {
        that.authenticatedMenuPages[index]['title'] = string;
      });
    }
  }
}

