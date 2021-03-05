import { IonicSelectableModule } from 'ionic-selectable';
import { BrowserModule } from '@angular/platform-browser';
import { ErrorHandler, NgModule } from '@angular/core';
import { IonicApp, IonicErrorHandler, IonicModule } from 'ionic-angular';
import { SplashScreen } from '@ionic-native/splash-screen';
import { StatusBar } from '@ionic-native/status-bar';
import { ScreenOrientation } from '@ionic-native/screen-orientation';
import { Stripe } from '@ionic-native/stripe';
import { PayPal } from '@ionic-native/paypal';
import { CacheModule } from "ionic-cache";
import { Geolocation } from '@ionic-native/geolocation';
import { NativeGeocoder } from '@ionic-native/native-geocoder';

import { MyApp } from './app.component';
import { AuthenticateProvider } from '../providers/authenticate/authenticate';
import { IonicStorageModule } from '@ionic/storage';

import { HttpClientModule, HttpClient } from '@angular/common/http';
import { HTTP } from '@ionic-native/http';

import { Facebook } from '@ionic-native/facebook';
import { GooglePlus } from '@ionic-native/google-plus';
import { NotifyProvider } from '../providers/notify/notify';
import { ParseErrorProvider } from '../providers/parse-error/parse-error';
import { DebugProvider } from '../providers/debug/debug';

import { NativeStorage } from '@ionic-native/native-storage';
import { StorageProvider } from '../providers/storage/storage';
import { AuthMiddlewareProvider } from '../providers/auth-middleware/auth-middleware';
import { NavigationProvider } from '../providers/navigation/navigation';

import { TranslateModule, TranslateLoader} from '@ngx-translate/core';
import { TranslateHttpLoader} from '@ngx-translate/http-loader';
import { TranslateProvider } from '../providers/translate/translate';

import { BarbersSortByPropertyProvider } from '../providers/barbers-sort-by-property/barbers-sort-by-property';
import { LoadingPopupProvider } from '../providers/loading-popup/loading-popup';
import { LibFunctionsProvider } from '../providers/lib-functions/lib-functions';
import { BooklyIntergrateProvider } from '../providers/bookly-intergrate/bookly-intergrate';
import { StripePaymentProvider } from '../providers/stripe-payment/stripe-payment';
import { PaypalPaymentProvider } from '../providers/paypal-payment/paypal-payment';
import { BaseRestProvider } from '../providers/base-rest/base-rest';
import { BasePageProvider } from '../providers/base-page/base-page';
import { BaseBookingPaymentProvider } from '../providers/base-booking-payment/base-booking-payment';


import { PlafformProvider } from '../providers/plafform/plafform';
import { AuthorizeProvider } from '../providers/authorize/authorize';

import { NativePageTransitions } from '@ionic-native/native-page-transitions';

export function createTranslateLoader(http: HttpClient) {
  return new TranslateHttpLoader(http, './assets/i18n/', '.json');
}

@NgModule({
  declarations: [
    MyApp
  ],
  imports: [
    BrowserModule,
    IonicModule.forRoot(MyApp),
    IonicStorageModule.forRoot(),
    HttpClientModule,
    TranslateModule.forRoot({
      loader: {
        provide: TranslateLoader,
        useFactory: (createTranslateLoader),
        deps: [HttpClient]
      }
    }),
    CacheModule.forRoot({ keyPrefix: 'cutaway-app-cache' }),
    IonicSelectableModule
  ],
  bootstrap: [IonicApp],
  entryComponents: [
    MyApp
  ],
  providers: [
    StatusBar,
    SplashScreen,
    NativePageTransitions,
    {provide: ErrorHandler, useClass: IonicErrorHandler},
    AuthenticateProvider,
    Facebook,
    GooglePlus,
    NotifyProvider,
    ParseErrorProvider,
    DebugProvider,
    NativeStorage,
    StorageProvider,
    AuthMiddlewareProvider,
    NavigationProvider,
    TranslateProvider,
    ScreenOrientation,
    BarbersSortByPropertyProvider,
    LoadingPopupProvider,
    LibFunctionsProvider,
    BooklyIntergrateProvider,
    Stripe,
    PayPal,
    StripePaymentProvider,
    PaypalPaymentProvider,
    BaseRestProvider,
    BasePageProvider,
    BaseBookingPaymentProvider,
    PlafformProvider,
    AuthorizeProvider,
    Geolocation,
    NativeGeocoder,
    HTTP
  ]
})
export class AppModule {}
