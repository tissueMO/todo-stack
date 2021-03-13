db = new Mongo().getDB("todo_stack");

db.createCollection('todos', {
  validator: {
    $jsonSchema: {
      bsonType: 'object',
      required: [
        'name',
        'limit',
        'hours',
        'priority',
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
        priority: {
          bsonType: 'int',
          minimum: 1,
          maximum: 9999,
          description: '優先度',
        },
      }
    }
  },
});
