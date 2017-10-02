import formErrors from './form-errors';
import formSuccess from './form-success';

export default function( Events ) {
  const formButtons = document.querySelectorAll( '.omg-form-submit-btn' );

  if ( ! formButtons || 0 === formButtons.length ) {
    return false;
  }

  [].forEach.call( formButtons, ( button ) => {
    const form = button.closest( 'form' );
    const formWrapper = button.closest( '.omg-form-wrapper' );

    form.addEventListener( 'submit', (e) => {
      e.preventDefault();

      const data = new FormData( form );
      data.append( 'formId', form.getAttribute( 'id' ) );

      if ( parseInt( formWrapper.dataset.rest ) ) {
        submitForm( data )
          .then( ( response ) => {
            handleFormErrors( response, formWrapper, form, Events );
            handleFormSuccess( response, formWrapper, form, Events );
          } )
          .catch( ( error) => {
            console.warn( error );
          });
      } else {
        Events.emit( 'omg-form-submit', {
          data: data,
          formWrapper: formWrapper,
          form: form
        } );
      }
    } );
  } );
}

const getFormElements = ( button ) => {
  const form = button.closest( 'form' );
  return form.querySelectorAll("[name^='omg-forms-']");
}

const getFormData = ( button ) => {
  const info = {};
  const elements = getFormElements( button );
  const form = button.closest( 'form' );

  info.form = form.getAttribute( 'id' );

  info.fields = [].map.call( elements, ( element ) => {
    return {
      value: element.value,
      type : getFieldType( element ),
      id: element.getAttribute( 'name' ),
      required: element.dataset.required
    }
  } );

  return info;
}

const getFieldType = ( field ) => {
  let type = field.getAttribute( 'type' );

  if ( type ) {
    return type;
  }
  type = field.tagName.toLowerCase();

  if ( type ) {
    return type;
  }

}

const handleFormErrors = ( response, formWrapper, form, Events ) => {
  if ( 'omg_form_validation_fail' === response.code ) {
    formErrors( response.data.fields );
    Events.emit( 'omg-form-field-errors', {
      fields: response.data.fields,
      formWrapper: formWrapper,
      form: form
    } );
  } else {
    return response;
  }
}

const handleFormSuccess = ( response, formWrapper, form, Events ) => {
  if ( true === response ) {
    formSuccess( formWrapper, form );
    Events.emit( 'omg-form-success', {
      formWrapper: formWrapper,
      form: form
    } );
  }
}

const submitForm = ( data ) => {
  const endpoint = `${OMGForms.baseURL}/wp-json/omg/v1/forms`;
  return new Promise( ( resolve, reject ) => {

		var xhr = new XMLHttpRequest();

		xhr.addEventListener("load", ( evt ) => {
            if ( xhr.readyState === 4 && xhr.status === 200 ) {
                return resolve( JSON.parse( xhr.response ) );
            }

            if ( xhr.readyState === 4 && xhr.status <= 400 ) {
                return reject( JSON.parse( xhr.response ) );
            }
		}, false);

		xhr.addEventListener("error", ( error ) => {
			return reject( JSON.parse( error ) );
		}, false);

		xhr.addEventListener("abort", ( error ) => {
			return reject( JSON.parse( error ) );
		}, false);

		xhr.open('POST', endpoint, true);

		xhr.setRequestHeader('X-WP-NONCE', OMGForms.nonce);
		xhr.send( data );
	})
}
