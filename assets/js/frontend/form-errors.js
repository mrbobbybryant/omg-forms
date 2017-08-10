export default function( errors ) {
  const fields = errors.map( (error) => document.getElementById( error ) );

  if ( 0 === fields.length ) {
    return false;
  }

  fields.forEach( ( field ) => field.classList.add( 'show' ) );
}
