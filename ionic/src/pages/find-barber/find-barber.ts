import { Component } from '@angular/core';
import { IonicPage, ModalController, Events, MenuController } from 'ionic-angular';
import { TranslateService } from '@ngx-translate/core';
import { Validators, FormBuilder, FormControl } from '@angular/forms';
import { CacheService } from "ionic-cache";
import { Geolocation } from '@ionic-native/geolocation';
import { NativeGeocoder, NativeGeocoderReverseResult, NativeGeocoderOptions } from '@ionic-native/native-geocoder';

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

//import { GoogleMapModalPage } from './../../pages/google-map-modal/google-map-modal';
import { ListServicesPage } from './../../pages/list-services/list-services';
import { StorageProvider } from '../../providers/storage/storage';

declare var google;

/**
 * Generated class for the FindBarberPage page.
 *
 * See https://ionicframework.com/docs/components/#navigation for more info on
 * Ionic pages and navigation.
 */

@IonicPage()
@Component({
  selector: 'page-find-barber',
  templateUrl: 'find-barber.html',
})
export class FindBarberPage extends BasePageProvider  {

  address;
  calendarSelectedDate: any;
  selectedService;
  listLocations;
  geoLocationStorageName: string = 'geo-location';
  geoLocationWatchPosSub: any = null;
  logo;

  constructor(
    public authProvider: AuthenticateProvider,
    public modalCtrl: ModalController,
    public translateCls: TranslateProvider,
    public formBuilder: FormBuilder,
    public event: Events,
    public nav: NavigationProvider,
    public loadingCtrl: LoadingPopupProvider,
    public bookly: BooklyIntergrateProvider,
    public libFuncs: LibFunctionsProvider,
    public notify: NotifyProvider,
    public parseErr: ParseErrorProvider,
    public menu: MenuController,
    public cache: CacheService,
    public authorize: AuthorizeProvider,
    public geolocation: Geolocation,
    public nativeGeocoder: NativeGeocoder,
    public storage: StorageProvider,
    translate: TranslateService
  ) {
    super(authProvider, nav, translateCls, loadingCtrl, notify, authorize);
    this.authorize.authenticate = 'login';
    this.authorize.authorize = 'customer';
    this.calendarSelectedDate = null;

    this.logo = this.libFuncs.getImageFollowOS('find_barber', 'logo');
  }

  ionViewDidLoad()
  {
    this.loadData();

    this.translateStrings['load_location_fail'] = 'Loading list locations failed.';
    this.translateStrings['date_required'] = 'Date is mandatory field.';
    this.translateStrings['date_invalid'] = 'Date selected must greater than current date.';
    this.translateStrings['service_required'] = 'Service is mandatory field.';

    this.validationMessages = {
      'location': [
        { type: 'required', message: 'Location is mandatory field.' }
      ],
      'address': [
        { type: 'required', message: 'Address is mandatory field.' }
      ]
    };

    this.translateCls.loadTranslateString('LOAD_LOCATIONS_FAIL').then(str => {
      this.translateStrings['load_location_fail'] = str;
    });
    this.translateCls.loadTranslateString('DATE_REQUIRED').then(str => {
      this.translateStrings['date_required'] = str;
    });
    this.translateCls.loadTranslateString('DATE_SELECTED_MUST_GREATER_THAN_CURRENT_DATE').then(str => {
      this.translateStrings['date_invalid'] = str;
    });
    this.translateCls.loadTranslateString('SERVICE_REQUIRED').then(str => {
      this.translateStrings['service_required'] = str;
    });
    this.translateCls.loadTranslateString('ADDRESS_REQUIRED').then(str => {
      this.validationMessages['location'][0]['message'] = str;
      this.validationMessages['address'][0]['message'] = str;
    });

    this.validationsForm = this.formBuilder.group({
      location: new FormControl('', Validators.compose([
        Validators.required
      ])),
      address: new FormControl('', Validators.compose([
        Validators.required
      ]))
    });

    this.resetData();
  }

  ionViewDidEnter()
  {
    this.changeStatusPushToNewPage(false);
    this.bookly.loadAuthenticatedData().then(() => {
      this.setupData(null);
    })
  }

  ionViewWillLeave()
  {
    if (this.geoLocationWatchPosSub != null) {
      this.geoLocationWatchPosSub.unsubscribe();
    }
    this.storage.remove(this.geoLocationStorageName);
  }

  setupData(event)
  {
    this.resetData();

    this.makePageIsLoading();
    this.getCurrentPosition().then(coordinate => {
      this.autoFillGeoAddress(coordinate['address']);
      let httpCall = this.bookly.getListBooklyLocations({
        lat: coordinate['lat'],
        lng: coordinate['lng'],
        distance: '3'
      });

      if (httpCall) {
        let cacheKey = httpCall.cache_key;
        let status = '';

        this.cache.loadFromObservable(cacheKey, httpCall.http).subscribe(response => {
          response = this.libFuncs.parseHttpResponse(response);

          if (response['status'] == 'ok') {
            if (response['data'].hasOwnProperty('locations')) {
              this.listLocations = this.libFuncs.convertObjectToArray(response['data']['locations']);
              status = 'ok';
            } else {
              status = 'fail';
            }
          } else {
            status = response['error'];
          }

          this.processAfterLoadListLocations(status, event, cacheKey);
        }, err => {
          if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
            status = 'authenticate_fail';
          } else {
            status = 'fail';
          }

          this.processAfterLoadListLocations(status, event, cacheKey);
        });
      } else {
        this.processAfterLoadListLocations('fail', event);
      }
    });
  }

  processAfterLoadListLocations(status, event, cacheKey = '')
  {
    let loaded = false;
    let errMsg = '';
    let clearCache = true;
    let closePageLoading = true;

    switch (status) {
      case 'ok':
        clearCache = !this.listLocations.length;
        loaded = true;
        break;
      case 'authenticate_fail':
        this.clearAuthenticatedStorage();
        closePageLoading = false;
        break;
      case 'fail':
      default:
        errMsg = this.translateStrings['load_location_fail'];
        break;
    }

    if (clearCache && cacheKey != '') {
      this.cache.removeItem(cacheKey);
    }

    if (closePageLoading) {
      this.makePageIsNotLoading();
    }

    if (loaded) {
      if (event == null) {
        let that = this;

        that.event.unsubscribe('calendar_selected_date');
        that.event.subscribe('calendar_selected_date', (calendarSelectedDate) => {
          that.calendarSelectedDate = calendarSelectedDate;
        });
        that.event.unsubscribe('calendar_go_to_previous_month');
        that.event.subscribe('calendar_go_to_previous_month', (month) => {
          that.calendarSelectedDate = null;
        });
        that.event.unsubscribe('calendar_go_to_next_month');
        that.event.subscribe('calendar_go_to_next_month', (month) => {
          that.calendarSelectedDate = null;
        });
      } else {
        event.complete();
      }
    } else if (errMsg != '') {
      this.notify.warningAlert(errMsg);
    }
  }

  resetData()
  {
    this.validationsForm.reset();
    this.selectedService = new Array();
    this.listLocations = new Array();
  }

  showGoogleMapModal() {
    /*let modal = this.modalCtrl.create(GoogleMapModalPage);
    let that = this;

    modal.onDidDismiss(data => {
      if (data && data.hasOwnProperty('description') && data.description != '') {
        that.address.place = data;
        that.getGoogleLatLong(data);
        that.validationsForm.controls.location.setValue(data.description);
      }
    });
    modal.present();*/
  }

  getGoogleLatLong(address:any)
  {
    let geocoder = new google.maps.Geocoder();
    let that = this;

    geocoder.geocode({ 'address': address.description }, (results, status) => {
      let latitude = results[0].geometry.location.lat();
      let longitude = results[0].geometry.location.lng();

      that.address.lat = latitude;
      that.address.long = longitude;
    });
  }

  initGoogleLocationData()
  {
    this.address = {
      place: '',
      lat: '',
      long: ''
    };
  }

  checkCalendarSelectedDateValid()
  {
    if (this.calendarSelectedDate == null) {
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
    let modal = that.modalCtrl.create(ListServicesPage, {
      'services': selectedServiceObj
    });

    modal.onDidDismiss(data => {
      if (data.status == 'authenticate_fail') {
        that.clearAuthenticatedStorage();
      } else if (data.status == 'complete') {
        that.selectedService = data.services;
      }
    });
    modal.present();

    return false;
  }

  onSearchBarber()
  {
    if (this.checkFormCanSubmit() && !this.checkPushedToNewPage()) {
      if (!this.checkCalendarSelectedDateValid()) {
        this.notify.warningAlert(this.translateStrings['date_invalid']);
      }/* else if (!this.selectedService.length) {
        this.notify.warningAlert(this.translateStrings['service_required']);
      }*/ else {
        this.changeStatusPushToNewPage(true);

        let dataSearch = {
          'location': this.validationsForm.controls['location'].value.name,
          'address': this.validationsForm.controls['address'].value
        };

        /*let selectedServicesId = new Array();
        for (let i = 0; i < this.selectedService.length; i++) {
          selectedServicesId.push(this.selectedService[i]['id']);
        }*/

        var month = this.calendarSelectedDate['month'];
        if (parseInt(month) < 10) {
          month = '0' + month;
        }
        var day = this.calendarSelectedDate['day'];
        if (parseInt(day) < 10) {
          day = '0' + day;
        }

        //dataSearch['service'] = selectedServicesId.join(',');
        dataSearch['work_time'] = this.calendarSelectedDate['year'] + '/' + month + '/' + day;

        this.nav.pushToPage('ListServicesPage', {
          'data_load_service': dataSearch
        });
      }
    }
  }

  getCurrentPosition()
  {
    return new Promise((resolve) => {
      this.storage.get(this.geoLocationStorageName).then(data => {
        if (data) {
          resolve(data);
        } else {
          this.usingGeoLocationToGetPosition().then(data => {
            resolve(data);
          });
        }
      }, err => {
        this.usingGeoLocationToGetPosition().then(data => {
          resolve(data);
        });
      });
    });
  }

  usingGeoLocationToGetPosition()
  {
    let coordinate = {
      'lat': 0,
      'lng': 0,
      'address': null
    };

    return new Promise((resolve) => {
      this.geolocation.getCurrentPosition({
        timeout: 5000
      }).then(geodata => {
        coordinate.lat = parseFloat(geodata.coords.latitude.toFixed(4));
        coordinate.lng = parseFloat(geodata.coords.longitude.toFixed(4));

        this.getGeoAddressFromLatLng(geodata.coords.latitude, geodata.coords.longitude)
        .then(address => {
          coordinate.address = address;
          this.storage.set(this.geoLocationStorageName, coordinate);

          this.geoLocationWatchPosSub = this.geolocation.watchPosition({
            timeout: 5000
          }).subscribe(geodata => {
            if (
              geodata &&
              geodata.hasOwnProperty('coords') &&
              geodata.coords.hasOwnProperty('latitude') &&
              geodata.coords.hasOwnProperty('longitude')
            ) {
              let coor = {
                'lat': 0,
                'lng': 0,
                'address': null
              };
              coor.lat = parseFloat(geodata.coords.latitude.toFixed(4));
              coor.lng = parseFloat(geodata.coords.longitude.toFixed(4));

              this.getGeoAddressFromLatLng(geodata.coords.latitude, geodata.coords.longitude)
              .then(address => {
                coor.address = address;
                this.autoFillGeoAddress(coor.address);
                this.storage.set(this.geoLocationStorageName, coor);
              });
            }
          });

          resolve(coordinate);
        });
      }, err => {
        resolve(coordinate);
      })
    });
  }

  getGeoAddressFromLatLng(lat, lng)
  {
    return new Promise((resolve) => {
      let options: NativeGeocoderOptions = {
        useLocale: true,
        maxResults: 1
      };
      this.nativeGeocoder.reverseGeocode(lat, lng, options)
      .then((result: NativeGeocoderReverseResult[]) => {
        let addressObject = result[0];
        let address = '';

        if (
          addressObject.hasOwnProperty('subThoroughfare') &&
          addressObject['subThoroughfare'] != ''
        ) {
          address += ' ' + addressObject['subThoroughfare'];
        }
        if (
          addressObject.hasOwnProperty('thoroughfare') &&
          addressObject['thoroughfare'] != ''
        ) {
          address += ' ' + addressObject['thoroughfare'];
        }
        if (
          addressObject.hasOwnProperty('subLocality') &&
          addressObject['subLocality'] != ''
        ) {
          address += ' ' + addressObject['subLocality'];
        }
        if (
          addressObject.hasOwnProperty('subAdministrativeArea') &&
          addressObject['subAdministrativeArea'] != ''
        ) {
          address += ' ' + addressObject['subAdministrativeArea'];
        }
        if (
          addressObject.hasOwnProperty('administrativeArea') &&
          addressObject['administrativeArea'] != ''
        ) {
          address += ' ' + addressObject['administrativeArea'];
        }

        address = address.trim();

        resolve(address);
      })
      .catch((error: any) => {
        resolve('');
      });
    });
  }

  autoFillGeoAddress(geoAddress: string)
  {
    let curAddress = this.validationsForm.controls.address.value;
    if (curAddress == null && curAddress != '') {
      this.validationsForm.controls.address.setValue(geoAddress);
    }
  }

}
