export default function( wrapper, form ) {
  const successMessage = wrapper.querySelector( '.omg-success' );

  if ( successMessage ) {
    form.classList.add( 'show' );
    successMessage.classList.add( 'show' );
    return true;
  }

  const redirect = wrapper.dataset.redirect;

  if ( redirect ) {
    window.location = redirect;
  }

}
