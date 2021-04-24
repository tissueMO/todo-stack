import edit from '@/pages/edit.vue';

import { createLocalVue, shallowMount, RouterLinkStub } from '@vue/test-utils';
import flushPromises from 'flush-promises';
import date from '@/plugins/filter';
describe('編集ページのテスト', () => {
  let wrapper;
  beforeEach(async () => {
    const localVue = createLocalVue();
    localVue.filter('date', date);
    wrapper = shallowMount(edit, {
      stubs: {
        NuxtLink: RouterLinkStub
      },
      mocks: {
        $config: { backendScheme: 'http', backendHost: 'localhost' },
        $axios: {
          get: () => {
            return new Promise((resolve) => {
              // if (mockError) throw Error('Mock error')
              resolve({
                data: [
                  {
                    created_at: '2021-04-24T14:04:24.091000Z',
                    hours: 2,
                    limit: '2021-04-27T03:00',
                    name: 'テスト',
                    priority: '1',
                    score: '-23',
                    updated_at: '2021-04-24T14:04:24.091000Z',
                    _id: 'a'
                  },
                  {
                    created_at: '2021-04-23T14:04:24.091000Z',
                    hours: 2,
                    limit: '2021-04-30T07:00',
                    name: 'テスト2',
                    priority: '2',
                    score: '-50',
                    updated_at: '2021-04-24T14:04:24.091000Z',
                    _id: 'fdsaf'
                  }
                ]
              });
            });
          }
        },
        localVue
      }
    });
    await flushPromises();
  });
  test('データ取れてるかな？？？？', () => {
    expect(wrapper.vm.todos).toMatchObject([
      {
        created_at: '2021-04-24T14:04:24.091000Z',
        hours: 2,
        limit: '2021-04-27T03:00',
        name: 'テスト',
        priority: '1',
        score: '-23',
        updated_at: '2021-04-24T14:04:24.091000Z',
        _id: 'a'
      },
      {
        created_at: '2021-04-23T14:04:24.091000Z',
        hours: 2,
        limit: '2021-04-30T07:00',
        name: 'テスト2',
        priority: '2',
        score: '-50',
        updated_at: '2021-04-24T14:04:24.091000Z',
        _id: 'fdsaf'
      }
    ]
    );
  });

  test('編集押したときitemに入るかな？その1', () => {
    wrapper.find('tbody tr:nth-child(1) button:nth-child(1)').trigger('click');
    expect(wrapper.vm.item).toMatchObject(wrapper.vm.todos[0]);
  });

  test('編集押したときitemに入るかな？その2', () => {
    wrapper.find('tbody tr:nth-child(2) button:nth-child(1)').trigger('click');
    expect(wrapper.vm.item).toMatchObject(wrapper.vm.todos[1]);
  });

  test('編集押したとき表示はうまく行ってるかな？', async () => {
    await wrapper.find('tbody tr:nth-child(1) button:nth-child(1)').trigger('click');
    console.log(wrapper.find('#name').element);
    expect(wrapper.find('#name').element.value).toBe(wrapper.vm.todos[0].name);
    expect(wrapper.find('#hours').element.value).toBe(wrapper.vm.todos[0].hours.toString());
    expect(wrapper.find('#limit').element.value).toBe(wrapper.vm.todos[0].limit);
  });
});
