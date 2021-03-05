import { Component } from '@angular/core';
import { IonicPage, NavParams } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';

import { BasePageProvider } from './../../providers/base-page/base-page';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { TranslateProvider } from './../../providers/translate/translate';
import { LoadingPopupProvider } from './../../providers/loading-popup/loading-popup';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';
import { NavigationProvider } from './../../providers/navigation/navigation';
import { NotifyProvider } from './../../providers/notify/notify';
import { BooklyIntergrateProvider } from './../../providers/bookly-intergrate/bookly-intergrate';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { AuthorizeProvider } from './../../providers/authorize/authorize';

/**
 * Generated class for the BarberDetailPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-barber-detail',
  templateUrl: 'barber-detail.html',
})
export class BarberDetailPage extends BasePageProvider {

  dataLoadBarber;
  barberDetail;
  timeSelected;
  blockTimesReceived;

  constructor(
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public loadingCtrl: LoadingPopupProvider,
    public navParams: NavParams,
    public libFuncs: LibFunctionsProvider,
    public nav: NavigationProvider,
    public notify: NotifyProvider,
    public bookly: BooklyIntergrateProvider,
    public parseErr: ParseErrorProvider,
    public authorize: AuthorizeProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';

    this.dataLoadBarber = this.navParams.data;
    this.timeSelected = '';
    this.barberDetail = null;
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['load_barber_fail'] = 'Loading barber failed.';
    this.translateCls.loadTranslateString('LOAD_BARBER_DETAIL_FAIL').then(str => {
      this.translateStrings['load_barber_fail'] = str;
    });
  }

  ionViewDidEnter()
  {
    this.changeStatusPushToNewPage(false);
    this.bookly.loadAuthenticatedData().then(() => {
      this.loadBarberDetail();
    });
  }

  loadBarberDetail()
  {
    let httpCall = this.bookly.loadStaffDetail(this.dataLoadBarber['data_load_service']);
    if (httpCall) {
      this.makePageIsLoading();
      let status = '';
      this.blockTimesReceived = '';

      httpCall.subscribe(response => {
        response = this.libFuncs.parseHttpResponse(response);

        if (response['status'] == 'ok') {
          if (response['data'].hasOwnProperty('staffDetail')) {
            let dataBarber = response['data']['staffDetail'];

            let blockTimes = {};
            if (dataBarber['block_times']) {
              let index = 0;
              for (let key in dataBarber['block_times']) {
                if (dataBarber['block_times'].hasOwnProperty(key)) {
                  blockTimes[index++] = dataBarber['block_times'][key]['start_time'];
                  if (this.blockTimesReceived == '') {
                    this.blockTimesReceived += dataBarber['block_times'][key]['start_time'];
                  } else {
                    this.blockTimesReceived += ',' + dataBarber['block_times'][key]['start_time'];
                  }
                }
              }
            }

            this.barberDetail = {
              'barber': {
                'logo': dataBarber['avatar'],
                'name': dataBarber['full_name'],
                'description': dataBarber['info'],
                'rating': dataBarber['rating_count'] + '/5',
                'services': dataBarber['service_count'],
                'euro': '€€',
                'pictures': dataBarber['gallery'],
                'is_favorite': dataBarber['is_favorite']
              },
              'service': {
                'price': dataBarber['service_price']
              },
              'times_list': blockTimes,
              'reviews': {}
            }

            this.barberDetail['reviews']['items'] = this.libFuncs.convertObjectToArray(dataBarber['reviews']);
            this.barberDetail['reviews']['total'] = this.barberDetail['reviews']['items'].length;

            for (var key = 0; key < this.barberDetail['reviews']['items'].length; key++) {
              this.barberDetail.reviews.items[key]['review'] = this.libFuncs.makeHtmlTrusted(this.barberDetail.reviews.items[key]['review']);
            }

            this.barberDetail['times_list'] = this.libFuncs.splitListToBlock(this.barberDetail['times_list']);
            this.barberDetail.barber.pictures = this.libFuncs.convertObjectToArray(this.barberDetail.barber.pictures);

            status = 'ok';
          } else {
            status = 'fail';
          }
        } else {
          status = response['error'];
        }

        this.processAfterLoadBarberDetail(status);
      }, err => {
        if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
          status = '';
        } else {
          status = 'fail';
        }

        this.processAfterLoadBarberDetail(status);
      });
    } else {
      this.processAfterLoadBarberDetail('fail');
    }
  }

  processAfterLoadBarberDetail(status)
  {
    let loaded = false;
    let errMsg = '';
    let closePageLoading = true;

    switch (status) {
      case 'ok':
        loaded = true;
        break;
      case 'authenticate_fail':
        this.clearAuthenticatedStorage();
        closePageLoading = false;
        break;
      case 'fail':
      default:
        errMsg = this.translateStrings['load_barber_fail'];
        break;
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

  pickTime(time)
  {
    this.timeSelected = time;
  }

  checkCanBookThisBarber()
  {
    return this.timeSelected != '';
  }

  bookThisBarber()
  {
    if (this.checkCanBookThisBarber() && !this.checkPushedToNewPage()) {
      this.changeStatusPushToNewPage(true);

      let dataBook = {
        'date': this.dataLoadBarber['data_load_service']['work_time'],
        'time': this.timeSelected,
        'block_times': this.blockTimesReceived,
        'data_load_service': this.dataLoadBarber['data_load_service']
      };

      dataBook['data_load_service'] = {...dataBook['data_load_service'], ...{'time': this.timeSelected}};

      this.nav.pushToPage('BarberBookSummaryPage', dataBook);
    }
  }

}
