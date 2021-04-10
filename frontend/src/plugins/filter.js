import Vue from 'vue';

Vue.filter('date', (value) => {
  return value.replace(/-/g, '/').replace('T', ' ');
});
