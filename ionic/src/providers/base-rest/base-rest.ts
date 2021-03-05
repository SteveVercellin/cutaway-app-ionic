import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { HTTP } from '@ionic-native/http';

import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { PlafformProvider } from './../../providers/plafform/plafform';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';

import { ServicesConfig } from './../../config/services';
import { Observable } from 'rxjs';

/*
  Generated class for the BaseRestProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class BaseRestProvider {

  authenticatedData;

  constructor(
    public http: HttpClient,
    public auth: AuthenticateProvider,
    public nativeHttp: HTTP,
    public libFunc: LibFunctionsProvider,
    public platform: PlafformProvider
  ) {
    this.authenticatedData = null;
  }

  loadAuthenticatedData()
  {
    return new Promise((resolve, reject) => {
      this.auth.readAuthenticatedStorage().then(data => {
        this.authenticatedData = data ? data : {};
        resolve(this.authenticatedData);
      }, err => {
        reject(err);
      });
    });
  }

  createPendingBooking(data)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.payments.create_pending_booking);

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

  protected makeHeaders()
  {
    let headers = new HttpHeaders();
    headers = this.makeAuthorizedHeader(headers, this.authenticatedData.token);
    headers = this.makeContentTypeJsonHeader(headers);

    return headers;
  }

  protected makeNativeHeaders()
  {
    let dataHeaders = {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ' + this.authenticatedData.token
    };

    return dataHeaders;
  }

  private makeAuthorizedHeader(headers, token)
  {
    return headers.set('Authorization', 'Bearer ' + token);
  }

  private makeContentTypeJsonHeader(headers)
  {
    return headers.set('Content-Type', 'application/json');
  }

}
