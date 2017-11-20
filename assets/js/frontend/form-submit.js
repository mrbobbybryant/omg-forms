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
      const formError = document.getElementById( 'omg-form-level-error' );

      if ( formError.classList.contains( 'error' ) ) {
        formError.classList.remove( 'error' );
      }

      data.append( 'formId', form.getAttribute( 'id' ) );

      if ( parseInt( formWrapper.dataset.rest ) ) {
          console.log(JSON.parse( formWrapper.dataset.formtype ));
        submitForm( data )
          .then( ( response ) => {
            handleFormSuccess( response, formWrapper, form, Events );
          } )
          .catch( ( error) => {
            handleFormErrors( error, formWrapper, form, Events );
            // console.warn( error );
          });
      } else {
        Events.emit( 'omg-form-submit', {
          data: data,
          formWrapper: formWrapper,
          form: form,
          formType: JSON.parse( formWrapper.dataset.formtype )
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

const handleFormErrors = ( error, formWrapper, form, Events ) => {
  if ( 'omg-form-field-error' === error.code ) {
    formErrors( error.data.fields );
    Events.emit( 'omg-form-field-errors', {
      fields: error.data.fields,
      formWrapper: formWrapper,
      form: form
    } );
  }

  if ( 'omg-form-submission-error' === error.code ) {
    const formError = document.getElementById( 'omg-form-level-error' );
    formError.innerHTML = error.message;
    formError.classList.add( 'error' );
    Events.emit( 'omg-form-submission-error', {
      error: error,
      formWrapper: formWrapper,
      form: form
    } );
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
