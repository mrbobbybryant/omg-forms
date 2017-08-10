import registerFormSubmit from './form-submit';
import BaseCss from '../../css/index.css';
import Events from './form-events';

document.addEventListener( 'DOMContentLoaded', () => {
  window.omg_events = Events;
  registerFormSubmit( window.omg_events );
} );
