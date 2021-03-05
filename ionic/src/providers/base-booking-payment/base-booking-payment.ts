import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Events } from 'ionic-angular';

import { BaseRestProvider } from './../../providers/base-rest/base-rest';
import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';

/*
  Generated class for the BaseBookingPaymentProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class BaseBookingPaymentProvider {

  translateStrings: any;
  dataLoadService: any;
  dataBook: any;
  dataPay: any;
  blockTimes: any;
  processing;

  constructor(
    public http: HttpClient,
    public baseRest: BaseRestProvider,
    public event: Events,
    public parseErr: ParseErrorProvider,
    public libs: LibFunctionsProvider
  ) {

  }

  loadData()
  {
    this.processing = false;
    this.baseRest.loadAuthenticatedData();
    this.generateDataPayFromDataBook();
  }

  createPendingBooking()
  {
    return new Promise((resolve) => {
      let dataPendingBook = {
        'customer_address': this.dataLoadService['address'],
        'location': this.dataLoadService['id_location'],
        'services': this.dataLoadService['service'],
        'staff': this.dataLoadService['staff'],
        'date': this.dataLoadService['work_time'],
        'time': this.dataLoadService['time'],
        'number_people': this.dataLoadService['number_people'],
        'block_times': this.blockTimes
      };

      let httpCall = this.baseRest.createPendingBooking(dataPendingBook);
      let groupBooking = null;
      let error = null;
      let created = false;

      if (httpCall) {
        httpCall.subscribe(response => {
          response = this.libs.parseHttpResponse(response);

          if (response['status'] == 'ok' && response['data'].hasOwnProperty('group')) {
            created = true;
            groupBooking = response['data']['group'];

            resolve({
              'created': created,
              'group': groupBooking,
              'error': null
            });
          } else {
            error = response['error'] == 'authenticate_fail' ? response['error'] : 'create_fail';

            resolve({
              'created': created,
              'group': groupBooking,
              'error': error
            });
          }
        }, err => {
          error = 'create_fail';
          if (this.parseErr.determineHttpAuthenticateFailedErrorResponse(err)) {
            error = 'authenticate_fail';
          }

          resolve({
            'created': created,
            'group': groupBooking,
            'error': error
          });
        });
      } else {
        resolve({
          'created': created,
          'group': groupBooking,
          'error': 'create_fail'
        });
      }
    });
  }

  generateDataPayFromDataBook()
  {
    let servicesTitle = new Array();
    let servicesPrice = 0;
    let numberPeople = this.dataBook['number_people'];
    for (let key in this.dataBook['services']) {
      if (this.dataBook['services'].hasOwnProperty(key)) {
        servicesTitle.push(this.dataBook['services'][key]['title']);
        servicesPrice += parseInt(this.dataBook['services'][key]['price']);
      }
    }
    servicesPrice *= numberPeople;

    let dataPayDescription = servicesTitle.join(' + ');
    dataPayDescription += ' - ' + this.dataBook['barber']['full_name'];
    dataPayDescription += ' - ' + this.dataBook['date']['month'] + '/' + this.dataBook['date']['day'] + '/' + this.dataBook['date']['year'];
    dataPayDescription += ' - ' + this.dataBook['time'];
    dataPayDescription += ' - ' + numberPeople + ' persone';

    this.dataPay = {
      'amount': servicesPrice,
      'currency': 'EUR',
      'description': dataPayDescription,
      'sku': 'book'
    };
  }

  triggerShowLoadingScreen()
  {
    this.event.publish('booking_show_loading_screen');
  }

  triggerHideLoadingScreen()
  {
    this.event.publish('booking_hide_loading_screen');
  }

  checkIsProcessing()
  {
    return this.processing;
  }

  changeStateProcessing(state)
  {
    this.processing = state;
  }

}
