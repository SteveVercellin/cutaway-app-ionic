import { Injectable } from '@angular/core';
import { Platform} from 'ionic-angular';

/*
  Generated class for the PlafformProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class PlafformProvider {

  constructor(public platform:Platform) {

  }

  determinePlatform()
  {
    if (!this.platform.is('core') && !this.platform.is('mobileweb')) {
      return 'app';
    } else {
      return 'browser';
    }
  }

  determineRunOnSpecificOS(osName: string)
  {
    return this.platform.is(osName);
  }

}
