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
          <td>{{ todo.limit | date }}</td>
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
    this.fetchTasks();
  },
  created () {
    // 30分に1回データ取得
    setInterval(() => {
      this.fetchTasks();
    }, 30 * 60 * 1000);
  },
  methods: {
    async fetchTasks () {
      const url = `${this.$config.backendScheme}://${this.$config.backendHost}/api/todos/?top=true`;
      await this.$axios.get(url).then((x) => {
        this.todos = x.data;
      });
    },
    async completeTask (index) {
      const srcTask = this.todos[index];
      await this.$axios.put(
        `${this.$config.backendScheme}://${this.$config.backendHost}/api/todos/${srcTask._id}`,
        {
          ...srcTask,
          hours: Number(srcTask.hours),
          priority: srcTask.priority ? Number(srcTask.priority) : undefined,
          limit: `${srcTask.limit}:00`,
          complete: true
        },
        {
          headers: {
            'Content-Type': 'application/json'
          }
        }
      ).then((res) => {
        this.fetchTasks();
      }).catch((err) => {
        alert('エラーが発生しました');
        console.error(err);
      });
    }
  }
};
</script>

<style lang='scss'>
.hours {
  width: 6rem;
}

.limit {
  width: 10rem;
}

.buttons {
  width: 7rem;
}
</style>
