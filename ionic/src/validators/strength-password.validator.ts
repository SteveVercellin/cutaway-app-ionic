import { FormControl } from '@angular/forms';

export class StrengthPasswordValidator {

    static invalid(control: FormControl){

    	let re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).+$/;
        let result = re.test(control.value);

        if (!result) {
        	return {
                invalid: true
            };
        }

        return null;
    }

}