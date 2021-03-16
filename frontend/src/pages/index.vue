<template>
  <div id="app" class="container">
    <h1>TODOリスト</h1>
    <NuxtLink to="/edit">
      編集ページへ
    </NuxtLink>
    <ul>
      <li>タスク名 | 所要時間 | 期限</li>
      <li v-for="(todo, index) in todos" :key="index">
        {{ todo.name }} | {{ todo.hours }} | {{ todo.limit }}
        <button class="btn btn-sm btn-info" @click="doneTask(index)">
          完了
        </button>
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
  created () {
    // 30分に1回データ取得
    setInterval(() => {
      this.getItem();
    }, 30 * 60 * 1000);
  },
  methods: {
    async getItem () {
      // データ取得のためのメソッド
      // /api/todos (GET) 一覧
      const url = 'http://localhost/api/todos/?top=true';
      await this.$axios.get(url).then((x) => { this.todos = x.data; });
      // console.log(this.todos[0]._id);
    },
    async doneTask (index) {
      // タスク完了のためのメソッド
      const cloneItem = { ...this.todos[index] };
      cloneItem.hours = Number(cloneItem.hours);
      if (cloneItem.priority) { cloneItem.priority = Number(cloneItem.priority); }
      cloneItem.limit += ':00';
      cloneItem.done = true;
      // console.log(cloneItem);
      const url = `http://localhost/api/todos/${cloneItem._id}`;
      await this.$axios.put(
        url,
        cloneItem,
        {
          headers: {
            'Content-Type': 'application/json'
          }
        }
      ).then((res) => {
        this.getItem();
      })
        .catch((err) => {
          alert('エラーが発生しました');
          console.error(err);
        });
    }
  }
};
</script>

<style>
</style>
