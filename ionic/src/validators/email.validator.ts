import { FormControl } from '@angular/forms';

export class EmailValidator {

    static invalid(control: FormControl){

    	let re = /^[a-zA-Z0-9._]+[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        let result = re.test(control.value);

        if (!result) {
        	return {
                invalid: true
            };
        }

        return null;
    }

}