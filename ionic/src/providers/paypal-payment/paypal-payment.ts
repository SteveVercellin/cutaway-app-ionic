import { HttpClient, HttpParams } from '@angular/common/http';
import { HTTP } from '@ionic-native/http';
import { Injectable } from '@angular/core';

import { ServicesConfig } from './../../config/services';

import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { BaseRestProvider } from './../../providers/base-rest/base-rest';
import { PlafformProvider } from './../../providers/plafform/plafform';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';

import { Observable } from 'rxjs';

/*
  Generated class for the PaypalPaymentProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class PaypalPaymentProvider extends BaseRestProvider {

  constructor(
    public http: HttpClient,
    public auth: AuthenticateProvider,
    public nativeHttp: HTTP,
    public libFunc: LibFunctionsProvider,
    public platform: PlafformProvider
  ) {
    super(http, auth, nativeHttp, libFunc, platform);
  }

  getNoncePay()
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let headers = this.makeHeaders();
      let params = new HttpParams();
      params = params.set('method', 'paypal');

      return this.http.get(ServicesConfig.payments.get_nonce, {
        headers: headers,
        params: params
      });
    } else {
      return false;
    }
  }

  completeBooking(data)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.payments.paypal.complete_booking);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        return this.http.post(serviceCall, data, {
          headers: headers
        });
      } else {
        let dataHeaders = this.makeNativeHeaders();
        data = this.libFunc.makeSureNativeHttpParamsValid(data);

        return Observable.fromPromise(this.nativeHttp.post(serviceCall, data, dataHeaders));
      }
    } else {
      return false;
    }
  }

}
