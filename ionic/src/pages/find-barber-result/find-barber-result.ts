import { Component } from '@angular/core';
import { IonicPage, NavParams, ActionSheetController } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';
import { CacheService } from "ionic-cache";

import { BasePageProvider } from './../../providers/base-page/base-page';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { TranslateProvider } from './../../providers/translate/translate';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { BarbersSortByPropertyProvider } from './../../providers/barbers-sort-by-property/barbers-sort-by-property';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';
import { BooklyIntergrateProvider } from './../../providers/bookly-intergrate/bookly-intergrate';
import { NotifyProvider } from './../../providers/notify/notify';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { AuthorizeProvider } from './../../providers/authorize/authorize';

/**
 * Generated class for the FindBarberResultPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-find-barber-result',
  templateUrl: 'find-barber-result.html',
})
export class FindBarberResultPage extends BasePageProvider {

  dataPass;
  listBarbers;
  sortedListBarbers;
  orderBarberOptions: any;
  orderBarberSelected: string;
  orderBarberSelectedHtml: string;

  constructor(
    public authProvider: AuthenticateProvider,
    public navParams: NavParams,
    public translateCls: TranslateProvider,
    public actionSheetCtrl: ActionSheetController,
    public arraySort: BarbersSortByPropertyProvider,
    public nav: NavigationProvider,
    public libFuncs: LibFunctionsProvider,
    public bookly: BooklyIntergrateProvider,
    public notify: NotifyProvider,
    public loadingCtrl: LoadingPopupProvider,
    public parseErr: ParseErrorProvider,
    public cache: CacheService,
    public authorize: AuthorizeProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';

    this.dataPass = this.navParams.data;
    this.listBarbers = this.sortedListBarbers = null;
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['order_placeholder'] = 'Order by';
    this.translateStrings['cancel'] = 'Cancel';
    this.translateStrings['find_barber_fail'] = 'Loading find barbers result failed.';

    this.orderBarberOptions = {
      0: {
        'label': 'Alphabet',
        'value': 'alphabet',
        'cssClass': 'order-order-alphabet'
      },
      /*1: {
        'label': this.translateStrings['order_price'],
        'value': 'price',
        'cssClass': 'order-price'
      },*/
      1: {
        'label': 'Rating',
        'value': 'rating',
        'cssClass': 'order-rating'
      }
    };

    this.translateCls.loadTranslateString('ORDER_BY').then(str => {
      this.translateStrings['order_placeholder'] = str;
    });
    this.translateCls.loadTranslateString('CANCEL').then(str => {
      this.translateStrings['cancel'] = str;
    });
    this.translateCls.loadTranslateString('FIND_BARBER_FAIL').then(str => {
      this.translateStrings['find_barber_fail'] = str;
    });
    this.translateCls.loadTranslateString('ALPHABET_ORDER').then(str => {
      this.orderBarberOptions[0]['label'] = str;
    });
    this.translateCls.loadTranslateString('RATING').then(str => {
      this.orderBarberOptions[1]['label'] = str;
    });
  }

  ionViewDidEnter()
  {
    this.changeStatusPushToNewPage(false);
    this.orderBarberSelected = this.orderBarberSelectedHtml = '';
    this.bookly.loadAuthenticatedData().then(() => {
      this.findBarbers();
    });
  }

  findBarbers()
  {
    let dataSearch = this.dataPass['data_load_service'];
    let httpCall = this.bookly.searchStaff(dataSearch);

    if (httpCall) {
      this.makePageIsLoading();
      let status = '';
      let cacheKey = httpCall.cache_key;

      this.cache.loadFromObservable(cacheKey, httpCall.http).subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          if (response['data'].hasOwnProperty('StaffMembers')) {
            this.listBarbers = this.libFuncs.convertObjectToArray(response['data']['StaffMembers']);
            this.sortedListBarbers = this.listBarbers;
            status = 'ok';
          } else {
            status = 'fail';
          }
        } else {
          status = response['error'];
        }

        this.processAfterFindBarbers(status, cacheKey);
      }, err => {
        if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
          status = 'authenticate_fail';
        } else {
          status = 'fail';
        }

        this.processAfterFindBarbers(status, cacheKey);
      });
    } else {
      this.processAfterFindBarbers('fail');
    }
  }

  processAfterFindBarbers(status, cacheKey = '')
  {
    let loaded = false;
    let errMsg = '';
    let clearCache = true;
    let closePageLoading = true;

    switch (status) {
      case 'ok':
        clearCache = !this.listBarbers.length;
        loaded = true;
        break;
      case 'authenticate_fail':
        this.clearAuthenticatedStorage();
        closePageLoading = false;
        break;
      case 'fail':
      default:
        errMsg = this.translateStrings['find_barber_fail'];
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

      let alertBox = that.notify.warningAlert(errMsg);
      alertBox.onDidDismiss(function (data, role) {
        that.nav.popBack();
      });
    }
  }

  goToBarberDetailPage(barber)
  {
    if (!this.checkPushedToNewPage()) {
      this.changeStatusPushToNewPage(true);

      let dataStaff = {
        'data_load_service': this.dataPass['data_load_service']
      };

      dataStaff['data_load_service'] = {...dataStaff['data_load_service'], ...{
        'staff': barber.id,
        'full_location': barber.location,
        'id_location': barber.location_id
      }};

      this.nav.pushToPage('BarberDetailPage', dataStaff);
    }
  }

  showOrderBarberOptionsBox()
  {
    let that = this;

    let actionSheet = that.actionSheetCtrl.create({
      title: that.translateStrings['order_placeholder']
    });

    for (let index in that.orderBarberOptions) {
      if (that.orderBarberOptions.hasOwnProperty(index)) {
        let cssClass = 'order-option';
        cssClass += ' ' + that.orderBarberOptions[index].cssClass;

        actionSheet.addButton({
          text: that.orderBarberOptions[index].label,
          cssClass: cssClass,
          handler: () => {
            that.orderBarberSelected = that.orderBarberOptions[index].value;
            that.orderBarberSelectedHtml = that.translateStrings['order_placeholder'] + ': ' + that.orderBarberOptions[index].label;
            that.reorderListBarbers();
          }
        });
      }
    }

    actionSheet.addButton({
      text: that.translateStrings['cancel'],
      role: 'cancel',
      handler: () => {}
    });

    actionSheet.present();
  }

  reorderListBarbers()
  {
    let propertyUse = '';

    if (this.sortedListBarbers.length) {
      switch (this.orderBarberSelected) {
        case 'alphabet':
          propertyUse = 'name';
          break;
        case 'price':
          propertyUse = 'price';
          break;
        case 'rating':
          propertyUse = 'rating';
          break;
      }

      this.arraySort.setUnsortData(this.listBarbers);

      if (propertyUse != '') {
        this.arraySort.setSortByProperty(propertyUse);
      }

      this.sortedListBarbers = this.arraySort.getSortedData();
    }
  }

}
