export default function( errors ) {
  const fields = errors.map( (error) => document.getElementById( error ) );

  if ( ! fields || 0 === fields.length ) {
    return false;
  }

  fields.forEach( ( field ) => {
    const input = field.querySelector( 'input' );
    const select = field.querySelector( 'select' );
    const textarea = field.querySelector( 'textarea' );

    field.classList.add( 'error' );

    if ( input ) {
        input.addEventListener( 'keyup', (e) => {
          if ( e.target.value ) {
            field.classList.remove( 'error' );
          }
        } );
    }

    if ( select ) {
        select.addEventListener( 'select', (e) => {
            field.classList.remove( 'error' );
        } );
    }

    if ( textarea ) {
        textarea.addEventListener( 'keyup', (e) => {
            if ( e.target.value ) {
                field.classList.remove( 'error' );
            }
        } );
    }

  } );

}
