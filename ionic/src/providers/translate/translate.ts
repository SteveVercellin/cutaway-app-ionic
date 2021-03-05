import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { TranslateService } from '@ngx-translate/core';

/*
  Generated class for the TranslateProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class TranslateProvider {

  constructor(
    public http: HttpClient,
    public translate: TranslateService
  ) {

  }

  loadTranslateString(key: string, params: Object = {})
  {
    return this.translate.get(key, params).toPromise();
  }

}
