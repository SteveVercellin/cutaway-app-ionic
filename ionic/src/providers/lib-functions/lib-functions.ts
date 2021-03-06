import { Injectable } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';

import { ParseErrorProvider } from './../../providers/parse-error/parse-error';
import { PlafformProvider } from './../../providers/plafform/plafform';

import { EnvConfig } from './../../env';

/*
  Generated class for the LibFunctionsProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class LibFunctionsProvider {

  constructor(
    public sanitizer: DomSanitizer,
    public parseErr: ParseErrorProvider,
    public platform: PlafformProvider
  ) {

  }

  convertObjectToArray(obj)
  {
    if (obj == null) {
      obj = {};
    }

    let objArray = Object.keys(obj).map(function(index){
      let item = obj[index];
      // do something with person
      return item;
    });

    return objArray;
  }

  checkDateGreaterThanCurrentDate(date)
  {
    let currentDate = new Date();
    let currentDateStr = currentDate.getFullYear() + '-' + (currentDate.getMonth() + 1) + '-' + currentDate.getDate();
    let currentDateNumber = Date.parse(currentDateStr);
    let dateStr = date['year'] + '-' + date['month'] + '-' + date['day'];
    let dateNumber = Date.parse(dateStr);

    return dateNumber > currentDateNumber;
  }

  convertDayToTwoChars(day)
  {
    day = parseInt(day);
    if (day < 10) {
      return '0' + day;
    }

    return day;
  }

  makeHtmlTrusted(html)
  {
    return this.sanitizer.bypassSecurityTrustHtml(html);
  }

  splitListToBlock(list, block: number = 4)
  {
    let newList = {};
    let count = -1;

    if (list == null) {
      return new Array();
    }

    for (let key in list) {
      if (list.hasOwnProperty(key)) {
        count++;
        let index = Math.floor(count / block);
        let subIndex = count - index * block;

        if (typeof newList[index] == 'undefined') {
          newList[index] = {};
        }

        newList[index][subIndex] = list[key];
      }
    }

    for (let key in newList) {
      if (newList.hasOwnProperty(key)) {
        newList[key] = this.convertObjectToArray(newList[key]);
      }
    }

    newList = this.convertObjectToArray(newList);

    return newList;
  }

  convertUnicodeToUnUnicode(str)
  {
    if (str != '') {
      str = str.replace(/??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???/g, "a");
      str = str.replace(/??|??|???|???|???|??|???|???|???|???|???/g, "e");
      str = str.replace(/??|??|???|???|??/g, "i");
      str = str.replace(/??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???/g, "o");
      str = str.replace(/??|??|???|???|??|??|???|???|???|???|???/g, "u");
      str = str.replace(/???|??|???|???|???/g, "y");
      str = str.replace(/??/g, "d");
      str = str.replace(/??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???/g, "A");
      str = str.replace(/??|??|???|???|???|??|???|???|???|???|???/g, "E");
      str = str.replace(/??|??|???|???|??/g, "I");
      str = str.replace(/??|??|???|???|??|??|???|???|???|???|???|??|???|???|???|???|???/g, "O");
      str = str.replace(/??|??|???|???|??|??|???|???|???|???|???/g, "U");
      str = str.replace(/???|??|???|???|???/g, "Y");
      str = str.replace(/??/g, "D");
    }

    return str;
  }

  parseHttpResponse(response: any)
  {
    let dataParsed = {
      status: '',
      error: '',
      data: {}
    };
    let statusesValid = ['ok', 'fail'];

    if (response) {
      if (response.hasOwnProperty("headers") && response.hasOwnProperty("data")) {
        response = JSON.parse(response['data']);
      }

      if (response.hasOwnProperty("status")) {
        let status = response['status'];
        if (statusesValid.indexOf(status) == -1) {
          status = 'fail';
        }
        dataParsed['status'] = status;

        if (response.hasOwnProperty("error")) {
          let error = '';
          if (this.parseErr.determineAuthenticateFailedErrorResponse(response)) {
            error = 'authenticate_fail';
          } else {
            error = response['error'];
          }
          dataParsed['error'] = error;
        }

        if (response.hasOwnProperty("data")) {
          dataParsed['data'] = response['data'];
        }
      } else {
        dataParsed['status'] = 'fail';
        dataParsed['error'] = 'fail';
      }
    } else {
      dataParsed['status'] = 'fail';
      dataParsed['error'] = 'fail';
    }

    return dataParsed;
  }

  makeSureNativeHttpParamsValid(data: Object)
  {
    for (var key in data) {
      if (data.hasOwnProperty(key)) {
        if (typeof data[key] != 'string') {
          data[key] = data[key].toString();
        }
      }
    }

    return data;
  }

  completeHttpService(relativeUrlService: string)
  {
    if (this.platform.determinePlatform() == 'app') {
      return EnvConfig.serviceRoot + relativeUrlService;
    } else {
      return EnvConfig.proxyServiceRoot + relativeUrlService;
    }
  }

  getImageFollowOS(page: string, name: string): string
  {
    let osName = this.platform.determineRunOnSpecificOS('ios') ? 'ios' : 'not_ios';
    let images = {
      'home': {
        'logo': {
          'not_ios': 'assets/imgs/global/logo.webp',
          'ios': 'assets/imgs/global/logo.png'
        }
      },
      'find_barber': {
        'logo': {
          'not_ios': 'assets/imgs/find-barber/logo.webp',
          'ios': 'assets/imgs/find-barber/logo.png'
        }
      }
    };

    if (images.hasOwnProperty(page)) {
      if (images[page].hasOwnProperty(name)) {
        return images[page][name][osName];
      }
    }

    return '';
  }

}
