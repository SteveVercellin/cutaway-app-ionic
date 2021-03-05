import { Injectable } from '@angular/core';
import { NativeStorage } from '@ionic-native/native-storage';
import { Storage } from '@ionic/storage';

import { PlafformProvider } from './../../providers/plafform/plafform';

/*
  Generated class for the StorageProvider provider.

  See https://angular.io/guide/dependency-injection for more info on providers
  and Angular DI.
*/
@Injectable()
export class StorageProvider {

  constructor(
    public platformProvider: PlafformProvider,
    public storage: Storage,
    public nativeStorage: NativeStorage
  ) {

  }

  get(key)
  {
    switch (this.platformProvider.determinePlatform()) {
      case 'app':
        return this.nativeStorage.getItem(key);
      case 'browser':
      default:
        return this.storage.get(key);

    }
  }

  set(key, data)
  {
    switch (this.platformProvider.determinePlatform()) {
      case 'app':
        return this.nativeStorage.setItem(key, data);
      case 'browser':
      default:
        return this.storage.set(key, data);

    }
  }

  remove(key)
  {
    switch (this.platformProvider.determinePlatform()) {
      case 'app':
        return this.nativeStorage.remove(key);
      case 'browser':
      default:
        return this.storage.remove(key);

    }
  }

}
