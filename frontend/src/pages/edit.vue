<template>
  <div id="app" class="container">
    <h1>編集ページ</h1>
    <NuxtLink to="/">
      トップへ
    </NuxtLink>
    <table class="table table-hover">
      <thead>
        <tr>
          <th>タスク名</th>
          <th>所要時間</th>
          <th>期限</th>
          <th />
          <th />
        </tr>
      </thead>
      <tbody>
        <tr v-for="(todo, index) in todos" :key="index">
          <td>{{ todo.name }}</td>
          <td>{{ todo.hours }}</td>
          <td>{{ todo.limit | date }}</td>
          <td>
            <button class="btn btn-sm btn-primary btn-block" @click="editTask(index)">
              編集
            </button>
          </td>
          <td>
            <button class="btn btn-sm btn-danger btn-block" @click="removeTask(todo._id)">
              削除
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="form-group">
      <label for="name">タスク名</label>
      <input id="name" v-model="targetTask.name" name="name" type="text">
    </div>
    <div class="form-group">
      <label for="hours">所要時間</label>
      <input id="hours" v-model="targetTask.hours" type="number" name="hours">
    </div>
    <div class="form-group">
      <label for="limit">期限</label>
      <input id="limit" v-model="targetTask.limit" type="datetime-local" name="limit">
    </div>
    <button class="btn btn-secondary" @click="pushTask">
      <span v-if="targetTask._id">
        編集
      </span>
      <span v-else>
        追加
      </span>
    </button>
  </div>
</template>

<script>
export default {
  data () {
    return {
      targetTask: {},
      todos: []
    };
  },
  mounted () {
    this.fetchTasks();
    this.resetTask();
  },
  methods: {
    resetTask () {
      this.targetTask = {
        _id: null,
        name: '',
        hours: 0,
        limit: ''
      };
    },
    async fetchTasks () {
      const url = `${this.$config.backendScheme}://${this.$config.backendHost}/api/todos`;
      await this.$axios.get(url).then((x) => {
        this.todos = x.data;
      });
    },
    async pushTask () {
      const id = this.targetTask._id;
      await this.$axios.request({
        url: `${this.$config.backendScheme}://${this.$config.backendHost}/api/todos${id ? `/${id}` : ''}`,
        method: (id ? 'put' : 'post'),
        data: {
          ...this.targetTask,
          hours: Number(this.targetTask.hours),
          limit: `${this.targetTask.limit}:00`
        },
        headers: {
          'Content-Type': 'application/json'
        }
      }).then(() => {
        this.fetchTasks();
        this.resetTask();
      }).catch((err) => {
        alert('エラーが発生しました');
        console.error(err);
      });
    },
    async removeTask (id) {
      const url = `${this.$config.backendScheme}://${this.$config.backendHost}/api/todos/${id}`;
      await this.$axios.delete(url)
        .then(() => {
          alert('削除しました');
          this.fetchTasks();
          this.resetTask();
        })
        .catch((err) => {
          alert('エラーが発生しました');
          console.error(err);
        });
    },
    editTask (index) {
      this.targetTask = { ...this.todos[index] };
    }
  }
};
</script>
