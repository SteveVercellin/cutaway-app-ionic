import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { HTTP } from '@ionic-native/http';
import { Observable } from 'rxjs';

import { ServicesConfig } from './../../config/services';
import { PaginateConfig } from './../../config/paginate';

import { AuthenticateProvider } from './../../providers/authenticate/authenticate';
import { BaseRestProvider } from './../../providers/base-rest/base-rest';
import { LibFunctionsProvider } from './../../providers/lib-functions/lib-functions';
import { PlafformProvider } from './../../providers/plafform/plafform';

/*
  Generated class for the BooklyIntergrateProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class BooklyIntergrateProvider extends BaseRestProvider {

  authenticatedData;

  constructor(
    public http: HttpClient,
    public auth: AuthenticateProvider,
    public libFunc: LibFunctionsProvider,
    public nativeHttp: HTTP,
    public platform: PlafformProvider
  ) {
    super(http, auth, nativeHttp, libFunc, platform);
  }

  getListBooklyServices()
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.get_list_services);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        return {
          'cache_key': 'get_list_bookly_services',
          'http': this.http.get(serviceCall, {
            headers: headers
          })
        };
      } else {
        let dataHeaders = this.makeNativeHeaders();

        return {
          'cache_key': 'get_list_bookly_services',
          'http': Observable.fromPromise(this.nativeHttp.get(serviceCall, {}, dataHeaders))
        };
      }
    } else {
      return false;
    }
  }

  searchStaff(dataSearch)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let cacheKey = 'search_staff';
      let searchParams = new HttpParams();
      for (var key in dataSearch) {
        if (dataSearch.hasOwnProperty(key)) {
          searchParams = searchParams.set(key, dataSearch[key]);

          let keyConverted = this.convertDataServiceToKey(key, dataSearch[key]);
          if (keyConverted != '') {
            cacheKey += ';' + keyConverted;
          }

          dataSearch[key] = dataSearch[key].toString();
        }
      }
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.search_staffs);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        return {
          'cache_key': cacheKey,
          'http': this.http.get(serviceCall, {
            params: searchParams,
            headers: headers
          })
        };
      } else {
        let dataHeaders = this.makeNativeHeaders();

        return {
          'cache_key': cacheKey,
          'http': Observable.fromPromise(this.nativeHttp.get(serviceCall, dataSearch, dataHeaders))
        };
      }
    } else {
      return false;
    }
  }

  loadStaffDetail(dataStaff)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.load_staff_detail);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        let searchParams = new HttpParams();
        for (var key in dataStaff) {
          if (dataStaff.hasOwnProperty(key)) {
            searchParams = searchParams.set(key, dataStaff[key]);
            dataStaff[key] = dataStaff[key].toString();
          }
        }

        return this.http.get(serviceCall, {
          params: searchParams,
          headers: headers
        });
      } else {
        let dataHeaders = this.makeNativeHeaders();

        return Observable.fromPromise(this.nativeHttp.get(serviceCall, dataStaff, dataHeaders));
      }
    } else {
      return false;
    }
  }

  loadStaffBookSummary(dataStaff)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let cacheKey = 'load_staff_book_summary';

      let params = new HttpParams();
      for (var key in dataStaff) {
        if (dataStaff.hasOwnProperty(key)) {
          params = params.set(key, dataStaff[key]);

          let keyConverted = this.convertDataServiceToKey(key, dataStaff[key]);
          if (keyConverted != '') {
            cacheKey += ';' + keyConverted;
          }

          dataStaff[key] = dataStaff[key].toString();
        }
      }
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.get_staff_book_summary);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        return {
          'cache_key': cacheKey,
          'http': this.http.get(serviceCall, {
            params: params,
            headers: headers
          })
        };
      } else {
        let dataHeaders = this.makeNativeHeaders();

        return {
          'cache_key': cacheKey,
          'http': Observable.fromPromise(this.nativeHttp.get(serviceCall, dataStaff, dataHeaders))
        };
      }
    } else {
      return false;
    }
  }

  loadCustomerOrders(page: number = 1, limit: number = PaginateConfig.limit)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.get_customer_orders);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();
        let params = new HttpParams();
        params = params.set('page', page.toString());
        params = params.set('limit', limit.toString());

        return this.http.get(serviceCall, {
          headers: headers,
          params: params
        });
      } else {
        let dataHeaders = this.makeNativeHeaders();
        let dataQuery = {
          'page': page.toString(),
          'limit': limit.toString()
        };

        return Observable.fromPromise(this.nativeHttp.get(serviceCall, dataQuery, dataHeaders));
      }
    } else {
      return false;
    }
  }

  loadCustomerOrder(group)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let cacheKey = 'load_customer_order';
      cacheKey += ';' + this.authenticatedData.user_email;
      let keyConverted = this.convertDataServiceToKey('group', group);
      if (keyConverted != '') {
        cacheKey += ';' + keyConverted;
      }
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.get_customer_order);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();
        let params = new HttpParams();
        params = params.set('group', group);

        return {
          'cache_key': cacheKey,
          'http': this.http.get(serviceCall, {
            params: params,
            headers: headers
          })
        };
      } else {
        let dataHeaders = this.makeNativeHeaders();
        let dataQuery = {
          'group': group.toString()
        };

        return {
          'cache_key': cacheKey,
          'http': Observable.fromPromise(this.nativeHttp.get(serviceCall, dataQuery, dataHeaders))
        };
      }
    } else {
      return false;
    }
  }

  loadCustomerHistories(page: number = 1, limit: number = PaginateConfig.limit)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.get_customer_histories);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();
        let params = new HttpParams();
        params = params.set('page', page.toString());
        params = params.set('limit', limit.toString());

        return this.http.get(serviceCall, {
          headers: headers,
          params: params
        });
      } else {
        let dataHeaders = this.makeNativeHeaders();
        let dataQuery = {
          'page': page.toString(),
          'limit': limit.toString()
        };

        return Observable.fromPromise(this.nativeHttp.get(serviceCall, dataQuery, dataHeaders));
      }
    } else {
      return false;
    }
  }

  loadListStaffsCustomerBooked()
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.get_staffs_customer_booked);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        return this.http.get(serviceCall, {
          headers: headers
        });
      } else {
        let dataHeaders = this.makeNativeHeaders();

        return Observable.fromPromise(this.nativeHttp.get(serviceCall, {}, dataHeaders));
      }
    } else {
      return false;
    }
  }

  makeStaffFavourite(staff)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let data = {
        'staff_id': staff.toString()
      };
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.make_staff_favourite);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        return this.http.post(serviceCall, data, {
          headers: headers
        });
      } else {
        let dataHeaders = this.makeNativeHeaders();
        return Observable.fromPromise(this.nativeHttp.post(serviceCall, data, dataHeaders));
      }
    } else {
      return false;
    }
  }

  makeStaffUnfavourite(staff)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let data = {
        'staff_id': staff.toString()
      };
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.make_staff_unfavourite);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        return this.http.post(serviceCall, data, {
          headers: headers
        });
      } else {
        let dataHeaders = this.makeNativeHeaders();
        return Observable.fromPromise(this.nativeHttp.post(serviceCall, data, dataHeaders));
      }
    } else {
      return false;
    }
  }

  getListBooklyLocations(dataFilter)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let cacheKey = 'get_list_bookly_locations';

      let filterParams = new HttpParams();
      for (var key in dataFilter) {
        if (dataFilter.hasOwnProperty(key)) {
          filterParams = filterParams.set(key, dataFilter[key]);

          let keyConverted = this.convertDataServiceToKey(key, dataFilter[key]);
          if (keyConverted != '') {
            cacheKey += ';' + keyConverted;
          }

          dataFilter[key] = dataFilter[key].toString();
        }
      }
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.get_list_locations);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        return {
          'cache_key': cacheKey,
          'http': this.http.get(serviceCall, {
            params: filterParams,
            headers: headers
          })
        };
      } else {
        let dataHeaders = this.makeNativeHeaders();

        return {
          'cache_key': cacheKey,
          'http': Observable.fromPromise(this.nativeHttp.get(serviceCall, dataFilter, dataHeaders))
        };
      }
    } else {
      return false;
    }
  }

  getStaffDashboardData()
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.load_staff_dashboard_data);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        return this.http.get(serviceCall, {
          headers: headers
        });
      } else {
        let dataHeaders = this.makeNativeHeaders();

        return Observable.fromPromise(this.nativeHttp.get(serviceCall, {}, dataHeaders));
      }
    } else {
      return false;
    }
  }

  updateStaffBookAvailable(state)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let data = {
        'state': state.toString()
      };
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.update_staff_book_available);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        return this.http.post(serviceCall, data, {
          headers: headers
        });
      } else {
        let dataHeaders = this.makeNativeHeaders();
        return Observable.fromPromise(this.nativeHttp.post(serviceCall, data, dataHeaders));
      }
    } else {
      return false;
    }
  }

  getStaffConfigAvailableTimeSlots(services, date)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let data = {
        'services': services,
        'date': date
      };
      let cacheKey = 'get_staff_config_avai_time_slots';
      cacheKey += ';' + this.authenticatedData.user_email;

      let params = new HttpParams();
      for (var key in data) {
        if (data.hasOwnProperty(key)) {
          params = params.set(key, data[key]);

          let keyConverted = this.convertDataServiceToKey(key, data[key]);
          if (keyConverted != '') {
            cacheKey += ';' + keyConverted;
          }

          data[key] = data[key].toString();
        }
      }
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.load_staff_config_available_time_slots);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        return {
          'cache_key': cacheKey,
          'http': this.http.get(serviceCall, {
            params: params,
            headers: headers
          })
        };
      } else {
        let dataHeaders = this.makeNativeHeaders();

        return {
          'cache_key': cacheKey,
          'http': Observable.fromPromise(this.nativeHttp.get(serviceCall, data, dataHeaders))
        };
      }
    } else {
      return false;
    }
  }

  updateStaffConfigAvailableTimeSlots(services, date, unAvaiTimeSlots)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let data = {
        'services': services.toString(),
        'date': date.toString(),
        'un_available_time_slots': unAvaiTimeSlots.toString()
      };
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.update_staff_config_available_time_slots);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        return this.http.post(serviceCall, data, {
          headers: headers
        });
      } else {
        let nativeHeaders = this.makeNativeHeaders();

        return Observable.fromPromise(this.nativeHttp.post(serviceCall, data, nativeHeaders));
      }
    } else {
      return false;
    }
  }

  loadCustomerOrdersReview(page: number = 1, limit: number = PaginateConfig.limit)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.get_customer_orders_review);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();
        let params = new HttpParams();
        params = params.set('page', page.toString());
        params = params.set('limit', limit.toString());

        return this.http.get(serviceCall, {
          headers: headers,
          params: params
        });
      } else {
        let dataHeaders = this.makeNativeHeaders();
        let dataQuery = {
          'page': page.toString(),
          'limit': limit.toString()
        };

        return Observable.fromPromise(this.nativeHttp.get(serviceCall, dataQuery, dataHeaders));
      }
    } else {
      return false;
    }
  }

  updateCustomerOrderReview(token, rating, comment)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let data = {
        'token': token.toString(),
        'rating': rating.toString(),
        'comment': comment.toString()
      };
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.set_customer_order_review);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        return this.http.post(serviceCall, data, {
          headers: headers
        });
      } else {
        let nativeHeader = this.makeNativeHeaders();

        return Observable.fromPromise(this.nativeHttp.post(
          serviceCall,
          data,
          nativeHeader
        ));
      }
    } else {
      return false;
    }
  }

  deleteCustomerOrderReview(token)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let data = {
        'token': token.toString()
      };
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.delete_customer_order_review);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();

        return this.http.post(serviceCall, data, {
          headers: headers
        });
      } else {
        let nativeHeader = this.makeNativeHeaders();

        return Observable.fromPromise(this.nativeHttp.post(
          serviceCall,
          data,
          nativeHeader
        ));
      }
    } else {
      return false;
    }
  }

  loadOrdersReview(staff: number, services: string)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.get_orders_review);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();
        let params = new HttpParams();
        params = params.set('staff', staff.toString());
        params = params.set('services', services);

        return this.http.get(serviceCall, {
          headers: headers,
          params: params
        });
      } else {
        let dataHeaders = this.makeNativeHeaders();
        let dataQuery = {
          'staff': staff.toString(),
          'services': services.toString()
        };

        return Observable.fromPromise(this.nativeHttp.get(
          serviceCall,
          dataQuery,
          dataHeaders
        ));
      }
    } else {
      return false;
    }
  }

  getStaffOrders(page: number = 1, limit: number = PaginateConfig.limit)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.get_staff_orders);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();
        let params = new HttpParams();
        params = params.set('page', page.toString());
        params = params.set('limit', limit.toString());

        return this.http.get(serviceCall, {
          headers: headers,
          params: params
        });
      } else {
        let dataHeaders = this.makeNativeHeaders();
        let dataQuery = {
          'page': page.toString(),
          'limit': limit.toString()
        };

        return Observable.fromPromise(this.nativeHttp.get(
          serviceCall,
          dataQuery,
          dataHeaders
        ));
      }
    } else {
      return false;
    }
  }

  getStaffOrder(id)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.get_staff_order);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();
        let params = new HttpParams();
        params = params.set('id', id.toString());

        return this.http.get(serviceCall, {
          headers: headers,
          params: params
        });
      } else {
        let dataHeaders = this.makeNativeHeaders();
        let dataQuery = {
          'id': id.toString()
        };

        return Observable.fromPromise(this.nativeHttp.get(
          serviceCall,
          dataQuery,
          dataHeaders
        ));
      }
    } else {
      return false;
    }
  }

  loadStaffOrdersReview(page: number = 1, limit: number = PaginateConfig.limit)
  {
    if (this.authenticatedData.hasOwnProperty('token') && this.authenticatedData.token != '') {
      let serviceCall = this.libFunc.completeHttpService(ServicesConfig.functions.bookly.get_staff_orders_review);

      if (this.platform.determinePlatform() == 'browser') {
        let headers = this.makeHeaders();
        let params = new HttpParams();
        params = params.set('page', page.toString());
        params = params.set('limit', limit.toString());

        return this.http.get(serviceCall, {
          headers: headers,
          params: params
        });
      } else {
        let dataHeaders = this.makeNativeHeaders();
        let dataQuery = {
          'page': page.toString(),
          'limit': limit.toString()
        };

        return Observable.fromPromise(this.nativeHttp.get(serviceCall, dataQuery, dataHeaders));
      }
    } else {
      return false;
    }
  }

  convertLocationNameToKey(location)
  {
    var key = this.makeSureVarIsString(location);

    key = key.trim();
    if (key != '') {
      key = this.libFunc.convertUnicodeToUnUnicode(key);
      key = key.replace(/[\s|\-]/g, '_');
      key = key.toLowerCase();
      key = key.replace(/[^a-z0-9_]/g, '');
    }

    return key;
  }

  convertServiceIdsToKey(services)
  {
    var key = this.makeSureVarIsString(services);

    key = key.trim();
    if (key != '') {
      key = key.replace(/,/g, '_');
      key = key.replace(/[^0-9_]/g, '');
    }

    return key;
  }

  convertDateToKey(date)
  {
    var key = this.makeSureVarIsString(date);

    key = key.trim();
    if (key != '') {
      key = key.replace(/[\-|/]/g, '_');
      key = key.replace(/[^0-9_]/g, '');
    }

    return key;
  }

  convertTimeToKey(time)
  {
    var key = this.makeSureVarIsString(time);

    key = key.trim();
    if (key != '') {
      key = key.replace(/[\s|:]/g, '_');
      key = key.replace(/[^0-9a-z_]/g, '');
    }

    return key;
  }

  convertCoordinateToKey(coordinate)
  {
    var key = this.makeSureVarIsString(coordinate);

    key = key.trim();
    if (key != '') {
      key = key.replace(/[\-|\.]/g, '_');
      key = key.replace(/[^0-9_]/g, '');
    }

    return key;
  }

  convertDataServiceToKey(keyService, dataService)
  {
    switch (keyService) {
      case 'location':
        return this.convertLocationNameToKey(dataService);
      case 'work_time':
        return this.convertDateToKey(dataService);
      case 'time':
        return this.convertTimeToKey(dataService);
      case 'service':
        return this.convertServiceIdsToKey(dataService);
      case 'lat':
      case 'lng':
      case 'distance':
        return this.convertCoordinateToKey(dataService);
      case 'staff':
      case 'group':
        return dataService;
      default:
        return '';
    }
  }

  makeSureVarIsString(variable)
  {
    if (typeof variable != 'string') {
      variable = variable.toString();
    }

    return variable;
  }

}
