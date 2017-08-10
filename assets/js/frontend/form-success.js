export default function( wrapper ) {
  const successMessage = wrapper.querySelector( '.omg-success' );

  if ( successMessage ) {
    successMessage.classList.add( 'show' );
    return;
  }

  const redirect = wrapper.dataset.redirect;

  if ( redirect ) {
    window.location = redirect;
  }

}
