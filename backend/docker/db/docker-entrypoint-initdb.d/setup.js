db = new Mongo().getDB("todo_stack");

db.createCollection('todos', {
  validator: {
    $jsonSchema: {
      bsonType: 'object',
      required: [
        'name',
        'limit',
        'hours',
      ],
      properties: {
        name: {
          bsonType: 'string',
          description: 'タスク名',
        },
        limit: {
          bsonType: 'date',
          description: '期日',
        },
        hours: {
          bsonType: 'int',
          minimum: 0,
          maximum: 9999,
          description: '所要時間',
        },
      }
    }
  },
});
