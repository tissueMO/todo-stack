import Vue from 'vue';

// APIから受け取った日付表示を，"yyyy-mm-dd HH:MM"で表示させる
Vue.filter('date', v => v.replace(/-/g, '/').replace('T', ' '));
