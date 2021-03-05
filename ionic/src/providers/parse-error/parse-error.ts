import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Injectable } from '@angular/core';

/*
  Generated class for the ParseErrorProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class ParseErrorProvider {

  constructor(public http: HttpClient) {

  }

  parseHttpErrorResponse(error: HttpErrorResponse)
  {
    if (error.hasOwnProperty('error')) {
      error = error.error;
    }

    if (error.hasOwnProperty('code')) {
      return error['code'];
    }

    return '';
  }

  determineHttpAuthenticateFailedErrorResponse(error: HttpErrorResponse)
  {
    let errCode = this.parseHttpErrorResponse(error);

    let isAuthenticateFailed = false;
    isAuthenticateFailed = isAuthenticateFailed || errCode == 'authenticate_fail';
    isAuthenticateFailed = isAuthenticateFailed || errCode == 'jwt_auth_no_auth_header';
    isAuthenticateFailed = isAuthenticateFailed || errCode == 'jwt_auth_bad_auth_header';
    isAuthenticateFailed = isAuthenticateFailed || errCode == 'jwt_auth_bad_config';
    isAuthenticateFailed = isAuthenticateFailed || errCode == 'jwt_auth_bad_iss';
    isAuthenticateFailed = isAuthenticateFailed || errCode == 'jwt_auth_bad_request';
    isAuthenticateFailed = isAuthenticateFailed || errCode == 'jwt_auth_invalid_token';

    return isAuthenticateFailed;
  }

  determineAuthenticateFailedErrorResponse(response: any)
  {
    let errCode = response.hasOwnProperty('error') ? response['error'] : '';

    let isAuthenticateFailed = false;
    isAuthenticateFailed = isAuthenticateFailed || errCode == 'authenticate_fail';
    isAuthenticateFailed = isAuthenticateFailed || errCode == 'jwt_auth_no_auth_header';
    isAuthenticateFailed = isAuthenticateFailed || errCode == 'jwt_auth_bad_auth_header';
    isAuthenticateFailed = isAuthenticateFailed || errCode == 'jwt_auth_bad_config';
    isAuthenticateFailed = isAuthenticateFailed || errCode == 'jwt_auth_bad_iss';
    isAuthenticateFailed = isAuthenticateFailed || errCode == 'jwt_auth_bad_request';
    isAuthenticateFailed = isAuthenticateFailed || errCode == 'jwt_auth_invalid_token';

    return isAuthenticateFailed;
  }

}
