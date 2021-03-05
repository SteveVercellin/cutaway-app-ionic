import { Component, Input } from '@angular/core';
import { Events } from 'ionic-angular';

/**
 * Generated class for the BarberBoxComponent component.
 *
 * See https://angular.io/api/core/Component for more info on Angular
 * Components.
 */
@Component({
  selector: 'barber-box',
  templateUrl: 'barber-box.html'
})
export class BarberBoxComponent {

  @Input() barber: any;

  constructor(
    public event: Events
  ) {
  }

  ngOnChanges()
  {
    this.mergeBarberWithDefaultValue();
  }

  mergeBarberWithDefaultValue()
  {
    let def = {
      'id': '',
      'logo': '',
      'name': '',
      'favourite': '',
      'begin': ''
    };

    this.barber = {...def, ...this.barber};
  }

  makeBarberFavourite(barber)
  {
    this.event.publish('barber_box_make_barber_favourite', barber);
  }

  makeBarberUnfavourite(barber)
  {
    this.event.publish('barber_box_make_barber_unfavourite', barber);
  }

}
