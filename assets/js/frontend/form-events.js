export default {
  events: {},
  subscribe: function (eventName, fn) {
    this.events[eventName] = this.events[eventName] || [];
    this.events[eventName].push(fn);
  },
  unsubscribe: function(eventName, fn) {
    if (this.events[eventName]) {
      this.events[ eventName ] = this.events[ eventName ].filter( function( eventFn ) {
        return eventFn !== fn;
      } );
    }
  },
  emit: function (eventName, data) {
    if (this.events[eventName]) {
      this.events[eventName].forEach(function(fn) {
        fn(data);
      });
    }
  }
};
