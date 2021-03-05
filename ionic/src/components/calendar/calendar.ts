import { Component, Input } from '@angular/core';
import { TranslateService } from '@ngx-translate/core';
import { Events } from 'ionic-angular';

import { TranslateProvider } from './../../providers/translate/translate';

/**
 * Generated class for the CalendarComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
  selector: 'calendar',
  templateUrl: 'calendar.html'
})
export class CalendarComponent {

  @Input() inputSelectedDate: any;
  @Input() forceSelectable: boolean;

  date: any;
  daysInThisMonth: any;
  daysInLastMonth: any;
  daysInNextMonth: any;
  monthNames: string[];
  currentMonth: any;
  currentYear: any;
  currentDate: any;
  chooseDate: any;
  selectedDate: any;

  constructor(
    public translateCls: TranslateProvider,
    public event: Events,
    translate: TranslateService
  ) {
    this.date = new Date();
    this.monthNames = new Array();
    this.getDaysOfMonth('init');
  }

  ngOnChanges()
  {
    if (this.checkHasInputSelected()) {
      this.date = new Date(this.inputSelectedDate['year'], this.inputSelectedDate['month'], 0);
    }
    this.getDaysOfMonth('init');
  }

  getDaysOfMonth(action: string)
  {
    this.chooseDate = null;
    this.selectedDate = false;
    if (!this.monthNames.length) {
      this.monthNames = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
      ];

      this.translateCls.loadTranslateString('JANUARY_LONG').then(str => {
        this.monthNames[0] = str;
      });
      this.translateCls.loadTranslateString('FEBRUARY_LONG').then(str => {
        this.monthNames[1] = str;
      });
      this.translateCls.loadTranslateString('MARCH_LONG').then(str => {
        this.monthNames[2] = str;
      });
      this.translateCls.loadTranslateString('APRIL_LONG').then(str => {
        this.monthNames[3] = str;
      });
      this.translateCls.loadTranslateString('MAY_LONG').then(str => {
        this.monthNames[4] = str;
      });
      this.translateCls.loadTranslateString('JUNE_LONG').then(str => {
        this.monthNames[5] = str;
      });
      this.translateCls.loadTranslateString('JULY_LONG').then(str => {
        this.monthNames[6] = str;
      });
      this.translateCls.loadTranslateString('AUGUST_LONG').then(str => {
        this.monthNames[7] = str;
      });
      this.translateCls.loadTranslateString('SEPTEMBER_LONG').then(str => {
        this.monthNames[8] = str;
      });
      this.translateCls.loadTranslateString('OCTOBER_LONG').then(str => {
        this.monthNames[9] = str;
      });
      this.translateCls.loadTranslateString('NOVEMBER_LONG').then(str => {
        this.monthNames[10] = str;
      });
      this.translateCls.loadTranslateString('DECEMBER_LONG').then(str => {
        this.monthNames[11] = str;
      });
    }

    this.daysInThisMonth = new Array();
    this.daysInLastMonth = new Array();
    this.daysInNextMonth = new Array();
    this.currentMonth = this.monthNames[this.date.getMonth()];
    this.currentYear = this.date.getFullYear();
    let currentDate = new Date();

    var firstDayThisMonth = new Date(this.date.getFullYear(), this.date.getMonth(), 1).getDay();
    var prevNumOfDays = new Date(this.date.getFullYear(), this.date.getMonth(), 0).getDate();
    for(let i = prevNumOfDays-(firstDayThisMonth-1); i <= prevNumOfDays; i++) {
      this.daysInLastMonth.push(i);
    }

    var thisNumOfDays = new Date(this.date.getFullYear(), this.date.getMonth()+1, 0).getDate();
    for (let i = 0; i < thisNumOfDays; i++) {
      this.daysInThisMonth.push(i+1);
    }

    var lastDayThisMonth = new Date(this.date.getFullYear(), this.date.getMonth()+1, 0).getDay();
    for (let i = 0; i < (6-lastDayThisMonth); i++) {
      this.daysInNextMonth.push(i+1);
    }
    var totalDays = this.daysInLastMonth.length+this.daysInThisMonth.length+this.daysInNextMonth.length;
    if(totalDays<36) {
      for(let i = (7-lastDayThisMonth); i < ((7-lastDayThisMonth)+7); i++) {
        this.daysInNextMonth.push(i);
      }
    }

    this.currentDate = 999;
    let hasInputSelected = this.checkHasInputSelected();
    let viewingCurrentDate = this.date.getMonth() === currentDate.getMonth() && this.date.getFullYear() === currentDate.getFullYear();

    if ((hasInputSelected && action == 'init') || viewingCurrentDate) {
      if (hasInputSelected && action == 'init') {
        this.currentDate = this.inputSelectedDate['day'];
      } else {
        this.currentDate = currentDate.getDate();
      }

      this.setSelectedDate(this.currentDate);
    }
  }

  goToLastMonth(day: any = null)
  {
    if (this.checkHasInputSelected() && !this.checkForceSelectable()) {
      return;
    }

    this.date = new Date(this.date.getFullYear(), this.date.getMonth(), 0);
    this.event.publish('calendar_go_to_previous_month', this.date.getMonth() + 1);
    this.getDaysOfMonth('jump');
    if (day != null) {
      this.setSelectedDate(day, 'choose');
    }
  }

  goToNextMonth(day: any = null)
  {
    if (this.checkHasInputSelected() && !this.checkForceSelectable()) {
      return;
    }

    this.date = new Date(this.date.getFullYear(), this.date.getMonth()+2, 0);
    this.event.publish('calendar_go_to_next_month', this.date.getMonth() + 1);
    this.getDaysOfMonth('jump');
    if (day != null) {
      this.setSelectedDate(day, 'choose');
    }
  }

  setSelectedDate(day, type: string = 'init')
  {
    if (this.checkHasInputSelected() && !this.checkForceSelectable()) {
      return;
    }

    if (type == 'choose') {
      this.chooseDate = day;
    }

    this.selectedDate = {
      'day': day,
      'month': this.date.getMonth() + 1,
      'year': this.date.getFullYear()
    };

    this.event.publish('calendar_selected_date', this.selectedDate);
  }

  setDayCellClass(day)
  {
    if (day == this.chooseDate) {
      return 'choose-day';
    } else if (day == this.currentDate) {
      return 'current-day'
    } else {
      return 'other-day'
    }
  }

  checkHasInputSelected()
  {
    return this.inputSelectedDate && this.inputSelectedDate.hasOwnProperty('day') && this.inputSelectedDate['day'] != '';
  }

  checkForceSelectable()
  {
    return typeof this.forceSelectable == 'boolean' && this.forceSelectable;
  }

}
