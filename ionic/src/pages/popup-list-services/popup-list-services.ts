import { Component } from '@angular/core';
import { IonicPage, ViewController, NavParams } from 'ionic-angular';
import { CacheService } from "ionic-cache";

import { TranslateService } from '@ngx-translate/core';
import { BasePageProvider } from './../../providers/base-page/base-page';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { TranslateProvider } from './../../providers/translate/translate';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { BooklyIntergrateProvider } from './../../providers/bookly-intergrate/bookly-intergrate';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';
import { NotifyProvider } from './../../providers/notify/notify';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { AuthorizeProvider } from './../../providers/authorize/authorize';

/**
 * Generated class for the PopupListServicesPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-popup-list-services',
  templateUrl: 'popup-list-services.html',
})
export class PopupListServicesPage extends BasePageProvider {

  booklyServices;
  loadedBooklyServices;
  selectedBooklyServices;

  constructor(
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public nav: NavigationProvider,
    public loadingCtrl: LoadingPopupProvider,
    public bookly: BooklyIntergrateProvider,
    public libFuncs: LibFunctionsProvider,
    public notify: NotifyProvider,
    public parseErr: ParseErrorProvider,
    public viewCtrl: ViewController,
    public navParams: NavParams,
    public cache: CacheService,
    public authorize: AuthorizeProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'staff';

    this.booklyServices = null;
    this.loadedBooklyServices = false;
    this.selectedBooklyServices = this.navParams.get('services');
    if (typeof this.selectedBooklyServices == 'undefined') {
      this.selectedBooklyServices = {};
    }
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['load_services_fail'] = 'Loading services failed.';
    this.translateStrings['service_required'] = 'Service is mandatory field.';

    this.translateCls.loadTranslateString('LOAD_SERVICES_FAIL').then(str => {
      this.translateStrings['load_services_fail'] = str;
    });
    this.translateCls.loadTranslateString('SERVICE_REQUIRED').then(str => {
      this.translateStrings['service_required'] = str;
    });
  }

  ionViewDidEnter()
  {
    this.bookly.loadAuthenticatedData().then(() => {
      this.loadBooklyServices();
    });
  }

  loadBooklyServices()
  {
    let httpCall = this.bookly.getListBooklyServices();

    if (httpCall) {
      this.makePageIsLoading();
      let cacheKey = httpCall.cache_key;
      let status = '';

      this.cache.loadFromObservable(cacheKey, httpCall.http).subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          if (response['data'].hasOwnProperty('services')) {
            this.booklyServices = this.libFuncs.convertObjectToArray(response['data']['services']);
            status = 'ok';
          } else {
            status = 'fail';
          }
        } else {
          status = response['error'];
        }

        this.processAfterLoadBooklyServices(status, cacheKey);
      }, err => {
        if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
          status = 'authenticate_fail';
        } else {
          status = 'fail';
        }

        this.processAfterLoadBooklyServices(status, cacheKey);
      });
    } else {
      this.processAfterLoadBooklyServices('fail');
    }
  }

  processAfterLoadBooklyServices(status, cacheKey = '')
  {
    let loaded = false;
    let errMsg = '';
    let clearCache = true;
    let closePageLoading = true;

    switch (status) {
      case 'ok':
        clearCache = !this.booklyServices.length;
        loaded = true;
        break;
      case 'authenticate_fail':
        this.viewCtrl.dismiss({
          'status': 'authenticate_fail'
        });
        closePageLoading = false;
        break;
      case 'fail':
      default:
        errMsg = this.translateStrings['load_services_fail'];
        break;
    }

    if (clearCache && cacheKey != '') {
      this.cache.removeItem(cacheKey);
    }

    if (closePageLoading) {
      this.makePageIsNotLoading();
    }

    if (!loaded && errMsg != '') {
      let that = this;

      that.makePageIsNotLoading();
      let alertBox = that.notify.warningAlert(errMsg);
      alertBox.onDidDismiss(function (data, role) {
        that.viewCtrl.dismiss({
          'status': 'load_fail'
        });
      });
    }
  }

  updateSelectService(service)
  {
    if (!this.selectedBooklyServices.hasOwnProperty(service.id)) {
      this.selectedBooklyServices[service.id] = {
        'id': service.id,
        'name': service.title
      };
    } else {
      delete this.selectedBooklyServices[service.id];
    }
  }

  closeSelectServices()
  {
    this.viewCtrl.dismiss({
      'status': 'close'
    });
  }

  completeSelectServices()
  {
    this.selectedBooklyServices = this.libFuncs.convertObjectToArray(this.selectedBooklyServices);
    this.selectedBooklyServices.sort(this.sortSelectedServices);

    if (this.selectedBooklyServices.length) {
      this.viewCtrl.dismiss({
        'status': 'complete',
        'services': this.selectedBooklyServices
      });
    } else {
      this.notify.warningAlert(this.translateStrings['service_required']);
    }
  }

  sortSelectedServices(item, nextItem)
  {
    let propertyUse = "name";

    if (!item.hasOwnProperty(propertyUse) || !nextItem.hasOwnProperty(propertyUse)) {
      return 0;
    }

    if (item[propertyUse] < nextItem[propertyUse]) {
      return -1;
    } else if (item[propertyUse] > nextItem[propertyUse]) {
      return 1;
    }

    return 0;
  }

}