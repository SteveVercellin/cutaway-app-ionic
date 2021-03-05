import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import {EnvConfig} from './../../env';

/*
  Generated class for the DebugProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class DebugProvider {

  constructor(public http: HttpClient) {

  }

  consoleDebug(debug: any)
  {
    if (EnvConfig.mode == 'debug') {
      console.log(debug);
    }
  }

  loopThroughtObject(object)
  {
    for (let key in object) {
      if (object.hasOwnProperty(key)) {
        console.log('Attribute: ' + key);
        if (typeof object[key] == 'object') {
          console.log('Value is object. Loop inside:');
          this.loopThroughtObject(object[key]);
        } else {
          console.log('Value: ' + object[key]);
        }
        console.log('End attribute: ' + key);
      }
    }
  }

}
