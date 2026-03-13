import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import EmptyState from '../../resources/js/app/components/shared/EmptyState.vue';

describe('EmptyState', () => {
  it('renders title and description', () => {
    const wrapper = mount(EmptyState, {
      props: {
        title: 'No items',
        description: 'Create your first item to get started.',
      },
    });
    expect(wrapper.text()).toContain('No items');
    expect(wrapper.text()).toContain('Create your first item to get started.');
  });

  it('renders action slot content', () => {
    const wrapper = mount(EmptyState, {
      props: { title: 'Empty' },
      slots: {
        action: '<button class="cta">Create</button>',
      },
    });
    expect(wrapper.find('button.cta').exists()).toBe(true);
    expect(wrapper.find('button.cta').text()).toBe('Create');
  });

  it('applies compact class when compact prop is true', () => {
    const wrapper = mount(EmptyState, {
      props: { title: 'Empty', compact: true },
    });
    expect(wrapper.find('div').classes()).toContain('py-8');
  });
});
