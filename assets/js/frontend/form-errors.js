export default function( errors ) {
  const fields = errors.map( (error) => document.getElementById( error ) );

  if ( ! fields || 0 === fields.length ) {
    return false;
  }

  fields.forEach( ( field ) => {
    const input = field.querySelector( 'input' );

    field.classList.add( 'error' );

    input.addEventListener( 'keyup', (e) => {
      if ( e.target.value ) {
        field.classList.remove( 'error' );
      }
    } );
  } );

}
