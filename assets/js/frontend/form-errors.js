export default function( errors ) {
  const fields = errors.map( (error) => document.getElementById( error ) );

  if ( 0 === fields.length ) {
    return false;
  }

  fields.forEach( ( field ) => {
    field.classList.add( 'show' );
    field.addEventListener( 'keyup', (e) => {
      if ( e.target.value ) {
        field.classList.remove( 'show' );
      }
    } );
  } );

}
