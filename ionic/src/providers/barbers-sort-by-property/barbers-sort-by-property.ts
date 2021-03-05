import { Injectable } from '@angular/core';

/*
  Generated class for the ArraySortByPropertyProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class BarbersSortByPropertyProvider {

  data;
  property;

  constructor() {
    this.data = new Array();
    this.property = '';
  }

  public setUnsortData(data)
  {
    this.data = data;
  }

  public setSortByProperty(property)
  {
    this.property = property;
  }

  public getSortedData()
  {
    let sortedData = this.data;
    if (sortedData.length) {
      switch (this.property) {
        case 'name':
          sortedData.sort(this._sortDataByName);
          break;
        case 'price':
          sortedData.sort(this._sortDataByPrice);
          break;
        case 'rating':
          sortedData.sort(this._sortDataByRating);
          break;
      }
    }

    return sortedData;
  }

  private _sortDataByName(item, nextItem)
  {
    let propertyUse = "full_name";

    if (!item.hasOwnProperty(propertyUse) || !nextItem.hasOwnProperty(propertyUse)) {
      return 0;
    }

    if (item[propertyUse] < nextItem[propertyUse]) {
      return -1;
    } else if (item[propertyUse] > nextItem[propertyUse]) {
      return 1;
    }

    return 0;
  }

  private _sortDataByPrice(item, nextItem)
  {
    let propertyUse = "price";

    if (!item.hasOwnProperty(propertyUse) || !nextItem.hasOwnProperty(propertyUse)) {
      return 0;
    }

    if (item[propertyUse] < nextItem[propertyUse]) {
      return -1;
    } else if (item[propertyUse] > nextItem[propertyUse]) {
      return 1;
    }

    return 0;
  }

  private _sortDataByRating(item, nextItem)
  {
    let propertyUse = "rating_count";

    if (!item.hasOwnProperty(propertyUse) || !nextItem.hasOwnProperty(propertyUse)) {
      return 0;
    }

    if (item[propertyUse] < nextItem[propertyUse]) {
      return -1;
    } else if (item[propertyUse] > nextItem[propertyUse]) {
      return 1;
    }

    return 0;
  }

}
