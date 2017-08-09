import formErrors from './form-errors';

export default function() {
  const formButtons = document.querySelectorAll( '.omg-form-submit-btn' );

  if ( ! formButtons || 0 === formButtons.length ) {
    return false;
  }

  [].forEach.call( formButtons, ( button ) => {
    const form = button.closest( 'form' );

    form.addEventListener( 'submit', (e) => {
      e.preventDefault();

      const data = new FormData( form );
      data.append( 'formId', form.getAttribute( 'id' ) );

      submitForm( data )
        .then( (response) => {
          if ( 'omg_form_validation_fail' === response.code ) {
            formErrors( response.data.fields );
          }
          console.log(response);
        })
        .catch( ( error) => {
          console.warn( error );
        });
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

const submitForm = ( data ) => {
  const endpoint = `${OMGForms.baseURL}/wp-json/wp/v2/entries`;
  return new Promise( ( resolve, reject ) => {

		var xhr = new XMLHttpRequest();

		xhr.addEventListener("load", ( evt ) => {
			return resolve( JSON.parse( xhr.response ) );
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
