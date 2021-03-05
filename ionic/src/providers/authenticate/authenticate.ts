import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { HTTP } from '@ionic-native/http';
import { Injectable } from '@angular/core';
import { ServicesConfig } from './../../config/services';
import { StorageProvider } from './../../providers/storage/storage';
import { PlafformProvider } from './../../providers/plafform/plafform';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';

import { Observable } from 'rxjs';

/*
  Generated class for the LoginProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class AuthenticateProvider {

  authenticateStorageName: string;

  constructor(
    public http: HttpClient,
    public storage: StorageProvider,
    public nativeHttp: HTTP,
    public libFunc: LibFunctionsProvider,
    public platform: PlafformProvider
  ) {
    this.authenticateStorageName = "auth_storage";
  }

  postLogin(data: Object)
  {
    let dataHeaders = {
      'Content-Type': 'application/json'
    };
    let serviceCall = this.libFunc.completeHttpService(ServicesConfig.authenticate.wp.login);

    if (this.platform.determinePlatform() == 'browser') {
      let headers = this.generateHttpHeader(dataHeaders);

      return this.http.post(serviceCall, data, {headers: headers});
    } else {
      data = this.libFunc.makeSureNativeHttpParamsValid(data);

      return Observable.fromPromise(this.nativeHttp.post(serviceCall, data, dataHeaders));
    }
  }

  fbGetPostLoginNonce()
  {
    let registerParams = new HttpParams();
    registerParams = registerParams.set('social', 'facebook');
    registerParams = registerParams.set('action', 'authenticate');

    return this.http.get(ServicesConfig.authenticate.social.get_nonce, {params: registerParams});
  }

  fbPostLogin(data: Object)
  {
    let dataHeaders = {
      'Content-Type': 'application/json'
    };
    let serviceCall = this.libFunc.completeHttpService(ServicesConfig.authenticate.social.authenticate);

    if (this.platform.determinePlatform() == 'browser') {
      let headers = this.generateHttpHeader(dataHeaders);

      return this.http.post(serviceCall, data, {headers: headers});
    } else {
      data = this.libFunc.makeSureNativeHttpParamsValid(data);

      return Observable.fromPromise(this.nativeHttp.post(serviceCall, data, dataHeaders));
    }
  }

  googleGetPostLoginNonce()
  {
    let registerParams = new HttpParams();
    registerParams = registerParams.set('social', 'google');
    registerParams = registerParams.set('action', 'authenticate');

    return this.http.get(ServicesConfig.authenticate.social.get_nonce, {params: registerParams});
  }

  googlePostLogin(data: Object)
  {
    let dataHeaders = {
      'Content-Type': 'application/json'
    };
    let serviceCall = this.libFunc.completeHttpService(ServicesConfig.authenticate.social.authenticate);

    if (this.platform.determinePlatform() == 'browser') {
      let headers = this.generateHttpHeader(dataHeaders);

      return this.http.post(serviceCall, data, {headers: headers});
    } else {
      data = this.libFunc.makeSureNativeHttpParamsValid(data);

      return Observable.fromPromise(this.nativeHttp.post(serviceCall, data, dataHeaders));
    }
  }

  getPostRegisterNonce()
  {
    let registerParams = new HttpParams();
    registerParams = registerParams.set('function', 'register');

    return this.http.get(ServicesConfig.functions.wp.get_nonce, {params: registerParams});
  }

  postRegister(data: Object)
  {
    let dataHeaders = {
      'Content-Type': 'application/json'
    };
    let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.wp.register);

    if (this.platform.determinePlatform() == 'browser') {
      let headers = this.generateHttpHeader(dataHeaders);

      return this.http.post(serviceCall, data, {headers: headers});
    } else {
      data = this.libFunc.makeSureNativeHttpParamsValid(data);

      return Observable.fromPromise(this.nativeHttp.post(serviceCall, data, dataHeaders));
    }
  }

  getSendResetPassNonce()
  {
    let registerParams = new HttpParams();
    registerParams = registerParams.set('function', 'send-reset-pass-link');

    return this.http.get(ServicesConfig.functions.wp.get_nonce, {params: registerParams});
  }

  sendResetPass(data: Object)
  {
    let dataHeaders = {
      'Content-Type': 'application/json'
    };
    let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.wp.send_reset_pass_email);

    if (this.platform.determinePlatform() == 'browser') {
      let headers = this.generateHttpHeader(dataHeaders);

      return this.http.post(serviceCall, data, {headers: headers});
    } else {
      data = this.libFunc.makeSureNativeHttpParamsValid(data);

      return Observable.fromPromise(this.nativeHttp.post(serviceCall, data, dataHeaders));
    }
  }

  getGetUserInformationNonce(token: string)
  {
    let params = new HttpParams();
    params = params.set('function', 'get-user-information');

    let headers = new HttpHeaders();
    headers = headers.set('Content-Type', 'application/json');
    headers = headers.set('Authorization', 'Bearer ' + token);

    return this.http.get(ServicesConfig.functions.wp.get_nonce, {
      params: params,
      headers: headers
    });
  }

  getUserInformation(nonce: string, token: string)
  {
    let params = new HttpParams();
    params = params.set('nonce', nonce);

    let dataHeaders = {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ' + token
    };
    let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.wp.get_user_information);

    if (this.platform.determinePlatform() == 'browser') {
      let headers = this.generateHttpHeader(dataHeaders);

      return this.http.get(serviceCall, {
        headers: headers
      });
    } else {
      return Observable.fromPromise(this.nativeHttp.get(serviceCall, {}, dataHeaders));
    }
  }

  getUpdateUserInformationNonce(token: string)
  {
    let params = new HttpParams();
    params = params.set('function', 'update-user-information');

    let headers = new HttpHeaders();
    headers = headers.set('Content-Type', 'application/json');
    headers = headers.set('Authorization', 'Bearer ' + token);

    return this.http.get(ServicesConfig.functions.wp.get_nonce, {
      params: params,
      headers: headers
    });
  }

  updateUserInformation(data: Object, token: string)
  {
    let dataHeaders = {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ' + token
    };
    let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.wp.update_user_information);

    if (this.platform.determinePlatform() == 'browser') {
      let headers = this.generateHttpHeader(dataHeaders);

      return this.http.post(serviceCall, data, {headers: headers});
    } else {
      data = this.libFunc.makeSureNativeHttpParamsValid(data);

      return Observable.fromPromise(this.nativeHttp.post(serviceCall, data, dataHeaders));
    }
  }

  getChangeUserPasswordNonce(token: string)
  {
    let params = new HttpParams();
    params = params.set('function', 'change-user-password');

    let headers = new HttpHeaders();
    headers = headers.set('Content-Type', 'application/json');
    headers = headers.set('Authorization', 'Bearer ' + token);

    return this.http.get(ServicesConfig.functions.wp.get_nonce, {
      params: params,
      headers: headers
    });
  }

  changeUserPassword(data: Object, token: string)
  {
    let dataHeaders = {
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ' + token
    };
    let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.wp.change_user_password);

    if (this.platform.determinePlatform() == 'browser') {
      let headers = this.generateHttpHeader(dataHeaders);

      return this.http.post(serviceCall, data, {headers: headers});
    } else {
      data = this.libFunc.makeSureNativeHttpParamsValid(data);

      return Observable.fromPromise(this.nativeHttp.post(serviceCall, data, dataHeaders));
    }
  }

  logAuthenticateError(data: Object)
  {
    let dataHeaders = {
      'Content-Type': 'application/json'
    };
    let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.wp.register);

    if (this.platform.determinePlatform() == 'browser') {
      let headers = this.generateHttpHeader(dataHeaders);

      return this.http.post(serviceCall, data, {headers: headers});
    } else {
      data = this.libFunc.makeSureNativeHttpParamsValid(data);

      return Observable.fromPromise(this.nativeHttp.post(serviceCall, data, dataHeaders));
    }
  }

  determineUserType(authenticatedData: any)
  {
    let type = 'customer';

    if (authenticatedData.hasOwnProperty('type')) {
      if (authenticatedData['type'] == 'staff') {
        type = 'staff';
      }
    }

    return type;
  }

  generateHttpHeader(dataHeaders)
  {
    let headers = new HttpHeaders();
    for (let key in dataHeaders) {
      if (dataHeaders.hasOwnProperty(key)) {
        headers = headers.set(key, dataHeaders[key]);
      }
    }

    return headers;
  }

  readAuthenticatedStorage()
  {
    return this.storage.get(this.authenticateStorageName);
  }

  writeAuthenticatedStorage(data: Object)
  {
    return this.storage.set(this.authenticateStorageName, data);
  }

  clearAuthenticatedStorage()
  {
    return this.storage.remove(this.authenticateStorageName);
  }

}
