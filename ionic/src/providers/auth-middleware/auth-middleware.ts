import { Injectable } from '@angular/core';
import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { AuthorizeProvider } from './../../providers/authorize/authorize';

/*
  Generated class for the AuthMiddlewareProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class AuthMiddlewareProvider {

  typeCheck: string;
  userLogIdentityCheck: string;

  constructor(
    public auth: AuthenticateProvider,
    public authorize: AuthorizeProvider
  ) {

  }

  async ionViewCanEnter()
  {
    this.typeCheck = this.authorize.authenticate;
    this.userLogIdentityCheck = this.authorize.authorize;

    try {
      let valid = await this.validateAuthenticate();
      return valid;
    } catch (err) {
      return false;
    }
  }

  validateAuthenticate()
  {
    return new Promise((resolve, reject) => {
      this.auth.readAuthenticatedStorage().then(data => {
        let valid = true;

        switch (this.typeCheck) {
          case 'logout':
            valid = data == null;
            break;
          case 'login':
          default:
            valid = data != null;
            if (valid) {
              valid = this.validateAuthorize(data);
            }
            break;
        }

        resolve(valid);
      }, err => {
        if (this.typeCheck == 'logout') {
          resolve(true);
        } else {
          reject(err);
        }
      });
    });
  }

  validateAuthorize(authenticateData)
  {
    let userType = this.auth.determineUserType(authenticateData);

    if (this.userLogIdentityCheck == '') {
      return userType == 'customer';
    }

    let userLogIdentityCheckArr = this.userLogIdentityCheck.split(',');
    let valid = false;

    for (let i = 0; i < userLogIdentityCheckArr.length; i++) {
      let identityCheck = userLogIdentityCheckArr[i];

      switch (identityCheck) {
        case 'customer':
          valid = userType == 'customer';
          break;
        case 'staff':
          valid = userType == 'staff';
          break;
      }

      if (valid) {
        break;
      }
    }

    return valid;
  }

}
