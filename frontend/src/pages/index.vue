<template>
  <div id="app" class="container">
    <h1>TODOリスト</h1>
    <NuxtLink to="/edit">
      編集ページへ
    </NuxtLink>
    <table class="table table-hover">
      <thead>
        <tr>
          <th class="tasks">
            タスク名
          </th>
          <th class="hours text-center">
            所要時間
          </th>
          <th class="limit">
            期限
          </th>
          <th class="buttons" />
        </tr>
      </thead>
      <tbody>
        <tr v-for="(todo, index) in todos" :key="index">
          <td>{{ todo.name }}</td>
          <td class="hours text-center">
            {{ todo.hours }}
          </td>
          <td>{{ todo.limit.replace(/-/g, '/').replace('T', ' ') }}</td>
          <td>
            <button class="btn btn-sm btn-info btn-block" @click="completeTask(index)">
              完了
            </button>
          </td>
        </tr>
      </tbody>
    </table>
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
      // /api/todos (GET) 一覧
      const url = 'http://localhost/api/todos/?top=true';
      await this.$axios.get(url).then((x) => { this.todos = x.data; });
      console.log(this.todos);
    },
    async completeTask (index) {
      const cloneItem = { ...this.todos[index] };
      cloneItem.hours = Number(cloneItem.hours);
      if (cloneItem.priority) { cloneItem.priority = Number(cloneItem.priority); }
      cloneItem.limit += ':00';
      cloneItem.complete = true;
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

<style lang='scss'>
.hours{
  width: 6rem;
}
.limit{
  width: 10rem;
}
.buttons{
  width: 7rem;
}
</style>
