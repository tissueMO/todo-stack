<template>
  <div id="app" class="container">
    <h1>追加ページ</h1>
    <ul>
      <li>タスク名 | 所要時間 | 期限</li>
      <li v-for="(todo, index) in todos" :key="index">
        {{ todo.name }} | {{ todo.hours }} | {{ todo.limit }}
        <button class="btn btn-sm btn-success" @click="editItem(index)">
          編集
        </button>
        <button :id="todo._id" class="btn btn-sm btn-danger" @click="deleteItem">
          削除
        </button>
      </li>
    </ul>
    <div class="form-group">
      <label for="name">タスク名</label>
      <input id="name" v-model="item.name" name="name" type="text">
    </div>
    <div class="form-group">
      <label for="hours">所要時間</label>
      <input id="hours" v-model="item.hours" type="number" name="hours">
    </div>
    <div class="form-group">
      <label for="limit">期限</label>
      <input id="limit" v-model="item.limit" type="datetime-local" name="limit">
    </div>
    <button @click="addItem">
      追加
    </button>
  </div>
</template>

<script>
export default {
  data () {
    return {
      item: { _id: null, name: '', hours: 0, limit: '' },
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
    },
    async addItem (e) {
      const cloneItem = { ...this.item };
      cloneItem.hours = Number(cloneItem.hours);
      cloneItem.limit += ':00';
      console.log(cloneItem);
      // /api/todos (POST) 新規追加
      // /api/todos/id (PUT/PATCH) 更新
      const url = 'http://localhost/api/todos' + (cloneItem._id ? `/${cloneItem._id}` : '');
      await this.$axios.request({
        url,
        method: (cloneItem._id ? 'put' : 'post'),
        data: cloneItem,
        headers: {
          'Content-Type': 'application/json'
        }
      }).then((res) => {
        this.getItem();
        this.item = { _id: null, name: '', hours: 0, limit: '' };
      })
        .catch((err) => {
          alert('エラーが発生しました');
          console.error(err);
        });
    },
    async deleteItem (e) {
      const id = e.currentTarget.id;
      console.log(id);
      // /api/todos/id (DELETE) 削除
      const url = `http://localhost/api/todos/${id}`;
      await this.$axios.delete(url)
        .then((res) => {
          alert('削除しました');
          this.getItem();
          this.item = { _id: null, name: '', hours: 0, limit: '' };
        })
        .catch((err) => {
          alert('エラーが発生しました');
          console.error(err);
        });
    },
    editItem (index) {
      this.item = { ...this.todos[index] };
      console.log(this.item);
    }
  }
};
</script>
