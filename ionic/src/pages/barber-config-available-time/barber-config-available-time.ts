import { Component } from '@angular/core';
import { IonicPage, Events, ModalController } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';
import { CacheService } from "ionic-cache";

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
import { StorageProvider } from './../../providers/storage/storage';

/**
 * Generated class for the BarberConfigAvailableTimePage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-barber-config-available-time',
  templateUrl: 'barber-config-available-time.html',
})
export class BarberConfigAvailableTimePage extends BasePageProvider {

  calendarSelectedDate: any;
  selectedService;
  timeSlots;
  unAvaiTimeSlots;
  cacheKey;
  detailLastSettingKey;
  dataAuthenticated;
  calendarDateFromLastSetting: any;

  constructor(
    public authProvider: AuthenticateProvider,
    public translateCls: TranslateProvider,
    public loadingCtrl: LoadingPopupProvider,
    public nav: NavigationProvider,
    public notify: NotifyProvider,
    public bookly: BooklyIntergrateProvider,
    public parseErr: ParseErrorProvider,
    public libFuncs: LibFunctionsProvider,
    public event: Events,
    public modalCtrl: ModalController,
    public cache: CacheService,
    public authorize: AuthorizeProvider,
    public storage: StorageProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'staff';
    this.calendarSelectedDate = {
      day: 0,
      month: 0,
      year: 0
    };
    this.calendarDateFromLastSetting = this.calendarSelectedDate;
    this.detailLastSettingKey = '_last_setting_barber_config_available_time';
    this.dataAuthenticated = null;
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['date_required'] = 'Date is mandatory field.';
    this.translateStrings['date_invalid'] = 'Date selected must greater than current date.';
    this.translateStrings['service_required'] = 'Service is mandatory field.';
    this.translateStrings['load_fail'] = 'Load data failed.';
    this.translateStrings['no_time'] = 'No time slots.';
    this.translateStrings['update_fail'] = 'Update data failed.';
    this.translateStrings['update_success'] = 'Updated data.';

    this.translateCls.loadTranslateString('DATE_REQUIRED').then(str => {
      this.translateStrings['date_required'] = str;
    });

    this.translateCls.loadTranslateString('DATE_SELECTED_MUST_GREATER_THAN_CURRENT_DATE').then(str => {
      this.translateStrings['date_invalid'] = str;
    });

    this.translateCls.loadTranslateString('SERVICE_REQUIRED').then(str => {
      this.translateStrings['service_required'] = str;
    });

    this.translateCls.loadTranslateString('BARBER_CONFIG_AVAILABLE_TIME_SLOTS_LOAD_FAIL').then(str => {
      this.translateStrings['load_fail'] = str;
    });

    this.translateCls.loadTranslateString('BARBER_CONFIG_AVAILABLE_TIME_SLOTS_NO_TIME').then(str => {
      this.translateStrings['no_time'] = str;
    });

    this.translateCls.loadTranslateString('BARBER_CONFIG_AVAILABLE_TIME_SLOTS_UPDATE_FAIL').then(str => {
      this.translateStrings['update_fail'] = str;
    });

    this.translateCls.loadTranslateString('BARBER_CONFIG_AVAILABLE_TIME_SLOTS_UPDATE_SUCCESS').then(str => {
      this.translateStrings['update_success'] = str;
    });

    this.resetData();
  }

  ionViewDidEnter()
  {
    this.bookly.loadAuthenticatedData().then((data) => {
      this.dataAuthenticated = data;

      this.cacheKey = '';
      this.setupData(null);
    }, err => {
      this.clearAuthenticatedStorage();
    });
  }

  setupData(event)
  {
    let that = this;

    this.resetData();

    this.readDetailLastSetting().then((setting) => {
      if (setting != null) {
        this.updateDataFromSetting(setting);
      }
    }).catch((err) => {
      console.log(err);
    });

    if (event == null) {
      that.event.unsubscribe('calendar_selected_date');
      that.event.subscribe('calendar_selected_date', (calendarSelectedDate) => {
        that.calendarSelectedDate = calendarSelectedDate;
        that.resetTimeSlots();
      });
      that.event.unsubscribe('calendar_go_to_previous_month');
      that.event.subscribe('calendar_go_to_previous_month', (month) => {
        that.calendarSelectedDate = {
          day: 0,
          month: 0,
          year: 0
        };
        that.resetTimeSlots();
      });
      that.event.unsubscribe('calendar_go_to_next_month');
      that.event.subscribe('calendar_go_to_next_month', (month) => {
        that.calendarSelectedDate = {
          day: 0,
          month: 0,
          year: 0
        };
        that.resetTimeSlots();
      });
    }

    if (event != null) {
      event.complete();
    }
  }

  checkCalendarSelectedDateValid()
  {
    if (this.calendarSelectedDate.day == 0) {
      return false;
    }

    return this.libFuncs.checkDateGreaterThanCurrentDate(this.calendarSelectedDate);
  }

  openListServices()
  {
    let that = this;
    let selectedServiceObj = {};
    for (let i = 0; i < that.selectedService.length; i++) {
      selectedServiceObj[that.selectedService[i]['id']] = {
        'id': that.selectedService[i]['id'],
        'name': that.selectedService[i]['name']
      };
    }
    let modal = that.modalCtrl.create('PopupListServicesPage', {
      'services': selectedServiceObj
    });

    modal.onDidDismiss(data => {
      if (data.status == 'authenticate_fail') {
        that.clearAuthenticatedStorage();
      } else if (data.status == 'complete') {
        that.selectedService = data.services;
        this.resetTimeSlots();
      }
    });
    modal.present();

    return false;
  }

  checkCanGetTimeSlots()
  {
    return this.checkCalendarSelectedDateValid() && this.selectedService.length;
  }

  getTimeSlots()
  {
    if (this.checkCanGetTimeSlots()) {
      this.resetTimeSlots();

      let dataService = this.buildDataService();

      let httpCall = this.bookly.getStaffConfigAvailableTimeSlots(
        dataService['services'],
        dataService['date']
      );

      if (httpCall) {
        this.makePageIsLoading();
        this.cacheKey = httpCall.cache_key;
        let status = '';

        this.cache.loadFromObservable(this.cacheKey, httpCall.http).subscribe(response => {
          response = this.libFuncs.parseHttpResponse(response);

          if (response['status'] == 'ok') {
            if (response['data'].hasOwnProperty('block_times')) {
              let blockTimes = this.libFuncs.convertObjectToArray(response['data']['block_times']);
              if (blockTimes.length) {
                while (blockTimes.length) {
                  let fourTimesBlock = blockTimes.splice(0, 4);
                  for (let i = 0; i < fourTimesBlock.length; i++) {
                    if (fourTimesBlock[i]['selected']) {
                      this.unAvaiTimeSlots += ';' + fourTimesBlock[i]['start_time'];
                    }
                  }
                  this.timeSlots.push(fourTimesBlock);
                }

                status = 'ok';
              } else {
                status = 'no_time';
              }
            } else {
              status = 'fail';
            }
          } else {
            status = response['error'];
          }

          this.processAfterGetTimeSlots(status);
        }, err => {
          if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
            status = 'authenticate_fail';
          } else {
            status = 'fail';
          }

          this.processAfterGetTimeSlots(status);
        });
      } else {
        this.processAfterGetTimeSlots('fail');
      }
    }
  }

  processAfterGetTimeSlots(status)
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
      case 'no_time':
        errMsg = this.translateStrings['no_time'];
        break;
      case 'fail':
      default:
        errMsg = this.translateStrings['load_fail'];
        break;
    }

    if (clearCache && this.cacheKey != '') {
      this.cache.removeItem(this.cacheKey);
    }

    if (closePageLoading) {
      this.makePageIsNotLoading();
    }

    if (!loaded) {
      this.notify.warningAlert(errMsg);
    }
  }

  pickTime(time)
  {
    time['selected'] = !time['selected'];
    if (time['selected']) {
      this.unAvaiTimeSlots += ';' + time['start_time'];
    } else {
      this.unAvaiTimeSlots = this.unAvaiTimeSlots.replace(';' + time['start_time'], '');
    }
  }

  updateUnAvailableTimeSlots()
  {
    if (this.checkCanGetTimeSlots()) {
      let dataService = this.buildDataService();

      let dataLastSetting = {
        'services': this.selectedService,
        'date': this.calendarSelectedDate,
        'un_vai_time_slots': this.unAvaiTimeSlots
      };

      let httpCall = this.bookly.updateStaffConfigAvailableTimeSlots(
        dataService['services'],
        dataService['date'],
        this.unAvaiTimeSlots
      );

      if (httpCall) {
        this.makePageIsLoading();
        let status = '';

        httpCall.subscribe(response => {
          response = this.libFuncs.parseHttpResponse(response);

          if (response['status'] == 'ok') {
            if (this.cacheKey != '') {
              this.cache.removeItem(this.cacheKey);
            }

            if (this.dataAuthenticated.hasOwnProperty('type_id')) {
              dataLastSetting['staff_id'] = this.dataAuthenticated['type_id'];
            }

            status = 'ok';
            this.writeDetailLastSetting(dataLastSetting).then(() => {
              this.processAfterUpdateUnAvailableTimeSlots(status);
            }).catch((err) => {
              console.log(err);
              this.processAfterUpdateUnAvailableTimeSlots(status);
            });
          } else {
            status = response['error'];
            this.processAfterUpdateUnAvailableTimeSlots(status);
          }
        }, err => {
          if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
            status = 'authenticate_fail';
          } else {
            status = 'fail';
          }

          this.processAfterUpdateUnAvailableTimeSlots(status);
        });
      } else {
        this.processAfterUpdateUnAvailableTimeSlots('fail');
      }
    }
  }

  processAfterUpdateUnAvailableTimeSlots(status)
  {
    let updated = false;
    let errMsg = '';
    let closePageLoading = true;

    switch (status) {
      case 'ok':
        updated = true;
        break;
      case 'authenticate_fail':
        this.clearAuthenticatedStorage();
        closePageLoading = false;
        break;
      case 'fail':
      default:
        errMsg = this.translateStrings['update_fail'];
        break;
    }


    if (closePageLoading) {
      this.makePageIsNotLoading();
    }

    if (updated) {
      this.notify.notifyToast(this.translateStrings['update_success']);
    } else if (errMsg != '') {
      this.notify.warningAlert(errMsg);
    }
  }

  buildDataService()
  {
    let selectedServicesId = new Array();
    for (let i = 0; i < this.selectedService.length; i++) {
      selectedServicesId.push(this.selectedService[i]['id']);
    }
    var month = this.calendarSelectedDate['month'];
    if (parseInt(month) < 10) {
      month = '0' + month;
    }
    var day = this.calendarSelectedDate['day'];
    if (parseInt(day) < 10) {
      day = '0' + day;
    }
    var year = this.calendarSelectedDate['year'];

    return {
      'services': selectedServicesId.join(','),
      'date': year + '-' + month + '-' + day
    };
  }

  resetData()
  {
    this.calendarSelectedDate = {
      day: 0,
      month: 0,
      year: 0
    };
    this.calendarDateFromLastSetting = this.calendarSelectedDate;
    this.selectedService = new Array();
    this.resetTimeSlots();
  }

  resetTimeSlots()
  {
    this.timeSlots = new Array();
    this.unAvaiTimeSlots = '';
  }

  readDetailLastSetting()
  {
    return new Promise((resolve, reject) => {
      this.storage.get(this.detailLastSettingKey).then((setting) => {
        resolve(setting);
      }).catch((err) => {
        reject(err);
      });
    });
  }

  writeDetailLastSetting(setting)
  {
    return new Promise((resolve, reject) => {
      this.storage.set(this.detailLastSettingKey, setting).then(() => {
        resolve();
      }).catch((err) => {
        reject(err);
      });
    });
  }

  updateDataFromSetting(setting)
  {
    if (
      setting.hasOwnProperty('date') &&
      setting.date.hasOwnProperty('day') &&
      setting.date.hasOwnProperty('month') &&
      setting.date.hasOwnProperty('year')
    ) {
      this.calendarSelectedDate = {
        day: setting.date.day,
        month: setting.date.month,
        year: setting.date.year
      };
      this.calendarDateFromLastSetting = this.calendarSelectedDate;
    }

    if (
      setting.hasOwnProperty('services')
    ) {
      this.selectedService = setting.services;
    }

    if (this.checkCanGetTimeSlots()) {
      this.getTimeSlots();
    }
  }

}
