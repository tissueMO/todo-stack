<template>
  <div id="app" class="container">
    <h1>TODOリスト</h1>
    <ul>
      <li>優先度 | タスク名 | 所要時間 | 期限</li>
      <li v-for="(todo, index) in todos" :key="index">
        {{ todo.priority }} | {{ todo.name }} | {{ todo.hour }} | {{ todo.limit }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data () {
    return {
      todos: []
    };
  },
  mounted () {
    this.getItem();
  },
  methods: {
    async getItem () {
      // /api/todos (GET) 一覧
      const url = 'http://localhost/api/todos';
      await this.$axios.get(url).then((x) => { this.todos = x.data; });
      console.log(this.todos[0]._id);
    }
  }
};
</script>

<style>
</style>
