import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { App, MenuController } from "ionic-angular";
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { NativePageTransitions, NativeTransitionOptions } from '@ionic-native/native-page-transitions';
import { PlafformProvider } from '../plafform/plafform';

/*
  Generated class for the NavigationProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class NavigationProvider {
  defaultPage: any;

  constructor(
    public http: HttpClient,
    public app: App,
    public auth: AuthenticateProvider,
    public menu: MenuController,
    public platform: PlafformProvider,
    public nativePageTransitions: NativePageTransitions
  ) {
    this.defaultPage = null;
  }

  setDefaultPage()
  {
    this.defaultPage = 'HomePage';
    this.auth.readAuthenticatedStorage().then(data => {
      if (data) {
        let userType = this.auth.determineUserType(data);
        switch (userType) {
          case 'customer':
            this.defaultPage = 'FindBarberPage';
            break;
          case 'staff':
            this.defaultPage = 'BarberDashboardPage';
            break;
        }
      }
    });
  }

  goToPage(page: any, data: any = {})
  {
    let that = this;
    let nav = that.getRootNav();
    if (that.defaultPage == null) {
      that.setDefaultPage();
    }

    if (this.platform.determinePlatform() == 'app') {
      let options: NativeTransitionOptions = {
        direction: 'up',
        duration: 400
      };

      this.nativePageTransitions.flip(options);
    }

    nav.setRoot(page, data).then(function (response) {
      if (!response) {
        nav.setRoot(that.defaultPage);
      }
    });
  }

  pushToPage(page: any, data: any = {})
  {
    let that = this;
    let nav = that.getRootNav();

    if (this.platform.determinePlatform() == 'app') {
      let options: NativeTransitionOptions = {
        direction: 'left',
        duration: 200, // in milliseconds (ms), default 400,
        slowdownfactor: 1, // overlap views (higher number is more) or no overlap (1), default 4
        iosdelay: 400, // ms to wait for the iOS webview to update before animation kicks in, default -1
        androiddelay: 400, // same as above but for Android, default -1
        winphonedelay: 400, // same as above but for Windows Phone, default -1,
      };

      this.nativePageTransitions.slide(options);
    }

    nav.push(page, data).then(function (response) {});
  }

  popBack()
  {
    let nav = this.app.getRootNav();

    if (this.platform.determinePlatform() == 'app') {
      let options: NativeTransitionOptions = {
        direction: 'right',
        duration: 200, // in milliseconds (ms), default 400,
        slowdownfactor: 1, // overlap views (higher number is more) or no overlap (1), default 4
        iosdelay: 400, // ms to wait for the iOS webview to update before animation kicks in, default -1
        androiddelay: 400, // same as above but for Android, default -1
        winphonedelay: 400, // same as above but for Windows Phone, default -1,
      };

      this.nativePageTransitions.slide(options);
    }

    nav.pop();
  }

  searchPageInStack(pageName)
  {
    let nav = this.getRootNav();
    let views = nav.getViews();
    let page = null;
    for (let key in views) {
      if (views.hasOwnProperty(key) && views[key]['name'] == pageName) {
        page = views[key];
      }
    }

    return page;
  }

  popToPage(pageName, data: any = {})
  {
    let nav = this.getRootNav();
    let page = this.searchPageInStack(pageName);
    if (page != null) {
      nav.popTo(page, data);
    }
  }

  getRootNav()
  {
    return this.app.getRootNavs()[0];
  }

  getMenuController()
  {
    return this.menu;
  }

}
