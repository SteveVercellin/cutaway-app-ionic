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
 * Generated class for the ListServicesPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-list-services',
  templateUrl: 'list-services.html',
})
export class ListServicesPage extends BasePageProvider {

  booklyServices;
  selectedBooklyServices;
  data;
  numberPeople;
  minPeople;
  maxPeople;

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
    this.authorize.authorize = 'customer';
    /*this.selectedBooklyServices = this.navParams.get('services');
    if (typeof this.selectedBooklyServices == 'undefined') {
      this.selectedBooklyServices = {};
    }*/
    this.numberPeople = 1;
    this.minPeople = 1;
    this.maxPeople = 10;
    this.data = this.navParams.data;
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['load_services_fail'] = 'Loading services failed.';
    this.translateStrings['service_required'] = 'Service is mandatory field.';
    this.translateStrings['number_people_required'] = 'The number of people is mandatory field.';
    this.translateStrings['number_people_validate'] = 'The number of people must be greater than or equal ' + this.minPeople + ' and less than or equal ' + this.maxPeople + '.';

    this.translateCls.loadTranslateString('LOAD_SERVICES_FAIL').then(str => {
      this.translateStrings['load_services_fail'] = str;
    });
    this.translateCls.loadTranslateString('SERVICE_REQUIRED').then(str => {
      this.translateStrings['service_required'] = str;
    });
    this.translateCls.loadTranslateString('NUMBER_PEOPLE_REQUIRED').then(str => {
      this.translateStrings['number_people_required'] = str;
    });
    this.translateCls.loadTranslateString('NUMBER_PEOPLE_LENGTH', {min: this.minPeople, max: this.maxPeople}).then(str => {
      this.translateStrings['number_people_validate'] = str;
    });

    this.resetData();
  }

  ionViewDidEnter()
  {
    this.changeStatusPushToNewPage(false);
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

      this.cache.loadFromObservable(httpCall.cache_key, httpCall.http).subscribe(response => {
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
        this.clearAuthenticatedStorage();
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
      this.notify.warningAlert(errMsg);
    }
  }

  updateSelectService(service)
  {
    /*if (!this.selectedBooklyServices.hasOwnProperty(service.id)) {
      this.selectedBooklyServices[service.id] = {
        'id': service.id,
        'name': service.title
      };
    } else {
      delete this.selectedBooklyServices[service.id];
    }*/

    if (!this.checkPushedToNewPage()) {
      if (this.numberPeople == '') {
        this.notify.warningAlert(this.translateStrings['number_people_required']);
      } else {
        this.numberPeople = parseInt(this.numberPeople);
        if (this.numberPeople < this.minPeople || this.numberPeople > this.maxPeople) {
          this.notify.warningAlert(this.translateStrings['number_people_validate']);
        } else {
          this.changeStatusPushToNewPage(true);

          let dataSearch = this.data['data_load_service'];

          let selectedServicesId = new Array();
          selectedServicesId.push(service.id);
          dataSearch['service'] = selectedServicesId.join(',');
          dataSearch['number_people'] = this.numberPeople;

          this.nav.pushToPage('FindBarberResultPage', {
            'data_load_service': dataSearch
          });
        }
      }
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

  resetData()
  {
    this.booklyServices = null;
    this.selectedBooklyServices = {};
  }

}
