import { Component } from '@angular/core';
import { IonicPage, NavParams } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';
import { Validators, FormBuilder, FormControl } from '@angular/forms';
import { CacheService } from "ionic-cache";

import { BasePageProvider } from './../../providers/base-page/base-page';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { TranslateProvider } from './../../providers/translate/translate';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';
import { BooklyIntergrateProvider } from './../../providers/bookly-intergrate/bookly-intergrate';
import { NotifyProvider } from './../../providers/notify/notify';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { AuthorizeProvider } from './../../providers/authorize/authorize';

/**
 * Generated class for the BarberBookSummaryPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-barber-book-summary',
  templateUrl: 'barber-book-summary.html',
})
export class BarberBookSummaryPage extends BasePageProvider {

  data;
  dataBook;

  constructor(
    public authProvider: AuthenticateProvider,
    public navParams: NavParams,
    public translateCls: TranslateProvider,
    public nav: NavigationProvider,
    public libFuncs: LibFunctionsProvider,
    public bookly: BooklyIntergrateProvider,
    public notify: NotifyProvider,
    public loadingCtrl: LoadingPopupProvider,
    public formBuilder: FormBuilder,
    public parseErr: ParseErrorProvider,
    public cache: CacheService,
    public authorize: AuthorizeProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';

    this.data = this.navParams.data;
    this.dataBook = null;
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['barber_not_found'] = 'Barber not found.';
    this.translateStrings['service_not_found'] = 'Service not found';
    this.translateStrings['time_selected_not_available'] = 'Booking time is not available.';
    this.translateStrings['load_data_fail'] = 'Load data failed.';

    this.translateCls.loadTranslateString('BARBER_NOT_FOUND').then(str => {
      this.translateStrings['barber_not_found'] = str;
    });

    this.translateCls.loadTranslateString('SERVICE_NOT_FOUND').then(str => {
      this.translateStrings['service_not_found'] = str;
    });

    this.translateCls.loadTranslateString('TIME_SELECTED_NOT_AVAILABLE').then(str => {
      this.translateStrings['time_selected_not_available'] = str;
    });

    this.translateCls.loadTranslateString('LOAD_DATA_BOOK_FAIL').then(str => {
      this.translateStrings['load_data_fail'] = str;
    });

    this.validationsForm = this.formBuilder.group({
      surname_report: new FormControl('', Validators.compose([])),
      the_floor: new FormControl('', Validators.compose([]))
    });
  }

  ionViewDidEnter()
  {
    this.changeStatusPushToNewPage(false);
    this.bookly.loadAuthenticatedData().then(() => {
      this.loadBarberBookSummary();
    }, err => {
      this.processAfterLoadBookSummary('fail');
    });
  }

  loadBarberBookSummary()
  {
    let httpCall = this.bookly.loadStaffBookSummary(this.data['data_load_service']);
    let status = '';

    if (httpCall) {
      this.makePageIsLoading();
      let cacheKey = httpCall.cache_key;
      this.cache.loadFromObservable(cacheKey, httpCall.http).subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          if (response['data'].hasOwnProperty('staffBookSummary')) {
            let staffBookSummary = response['data']['staffBookSummary'];
            let date = this.data['date'];
            date = date.split("/");

            this.dataBook = {
              'barber': staffBookSummary['barber'],
              'services': staffBookSummary['services'],
              'date': {
                'day': date[2],
                'month': date[1],
                'year': date[0]
              },
              'time': this.data['time'],
              'number_people': this.data['data_load_service']['number_people']
            };

            this.dataBook['services'] = this.libFuncs.convertObjectToArray(this.dataBook['services']);
            status = 'ok';
          } else {
            status = 'fail';
          }
        } else {
          status = response['error'];
        }

        this.processAfterLoadBookSummary(status, cacheKey);
      }, err => {
        if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
          status = 'authenticate_fail';
        } else {
          status = this.parseErr.parseHttpErrorResponse(err);
        }

        this.processAfterLoadBookSummary(status, cacheKey);
      });
    }
  }

  processAfterLoadBookSummary(status, cacheKey = '')
  {
    let loaded = false;
    let errMsg = '';
    let clearCache = true;
    let closePageLoading = true;

    switch (status) {
      case 'ok':
        clearCache = false;
        loaded = true;
        break;
      case 'authenticate_fail':
        this.clearAuthenticatedStorage();
        loaded = true;
        closePageLoading = false;
        break;
      case 'time_not_available':
        errMsg = this.translateStrings['time_selected_not_available'];
        break;
      case 'barber_not_found':
        errMsg = this.translateStrings['barber_not_found'];
        break;
      case 'service_not_found':
        errMsg = this.translateStrings['service_not_found'];
        break;
      case 'data_missing':
      default:
        errMsg = this.translateStrings['load_data_fail'];
        break;
    }

    if (clearCache && cacheKey != '') {
      this.cache.removeItem(cacheKey);
    }

    if (closePageLoading) {
      this.makePageIsNotLoading();
    }

    if (!loaded) {
      let that = this;
      let alertBox = that.notify.warningAlert(errMsg);
      alertBox.onDidDismiss(function (data, role) {
        that.nav.popBack();
      });
    }
  }

  onCompletePurchase()
  {
    if (this.checkFormCanSubmit() && !this.checkPushedToNewPage()) {
      this.changeStatusPushToNewPage(true);

      let dataLoadService = {...this.data['data_load_service'], ...this.validationsForm.value};
      let servicesPrice = 0;
      let numberPeople = this.dataBook['number_people'];
      for (let key in this.dataBook['services']) {
        if (this.dataBook['services'].hasOwnProperty(key)) {
          servicesPrice += parseInt(this.dataBook['services'][key]['price']);
        }
      }
      servicesPrice *= numberPeople;

      /*let servicesTitle = new Array();
      let servicesPrice = 0;
      for (let key in this.dataBook['services']) {
        if (this.dataBook['services'].hasOwnProperty(key)) {
          servicesTitle.push(this.dataBook['services'][key]['title']);
          servicesPrice += parseInt(this.dataBook['services'][key]['price']);
        }
      }
      let dataPayDescription = servicesTitle.join(' + ');
      dataPayDescription += ' - ' + this.dataBook['barber']['full_name'];
      dataPayDescription += ' - ' + this.dataBook['date']['month'] + '/' + this.dataBook['date']['day'] + '/' + this.dataBook['date']['year'];
      dataPayDescription += ' - ' + this.dataBook['time'];

      let dataPay = {
        'amount': servicesPrice,
        'currency': 'EUR',
        'description': dataPayDescription,
        'sku': 'book'
      };*/
      this.nav.pushToPage('BarberBookCompletePage', {
        'data_load_service': dataLoadService,
        'data_book': this.dataBook,
        'amount': servicesPrice,
        'block_times': this.data['block_times']
      });
    }
  }

}
