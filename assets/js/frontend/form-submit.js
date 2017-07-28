export default function() {
  const formButtons = document.querySelectorAll( '.omg-form-submit-btn' );

  if ( ! formButtons || 0 === formButtons.length ) {
    return false;
  }

  [].forEach.call( formButtons, ( button ) => {
    button.addEventListener( 'click', (e) => {
      e.preventDefault();
      const data = getFormData( button );

      submitForm( data )
        .then( (response) => {
          console.log(response);
        })
        .catch( ( error) => {
          console.log(error);
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

  info.form = form.getAttribute( 'name' );

  info.fields = [].map.call( elements, ( element ) => {
    return {
      value: element.value,
      type : getFieldType( element ),
      name: element.getAttribute( 'name' ),
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
		xhr.setRequestHeader('Content-Type', 'application/json');
		xhr.send( JSON.stringify( data ) );
	})
}
