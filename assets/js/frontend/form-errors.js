export default function( errors ) {
  const fields = errors.map( (error) => document.getElementById( error ) );

  if ( 0 === fields.length ) {
    return false;
  }
  console.log( fields )
  fields.forEach( ( field ) => field.classList.add( 'show' ) );
}
