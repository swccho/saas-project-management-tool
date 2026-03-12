import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import Button from '../../resources/js/app/components/ui/Button.vue';

describe('Button', () => {
  it('renders slot content', () => {
    const wrapper = mount(Button, {
      slots: { default: 'Click me' },
    });
    expect(wrapper.text()).toContain('Click me');
  });

  it('shows loading state when loading prop is true', () => {
    const wrapper = mount(Button, {
      props: { loading: true },
      slots: { default: 'Submit' },
    });
    expect(wrapper.find('span.animate-spin').exists()).toBe(true);
  });

  it('is disabled when disabled prop is true', () => {
    const wrapper = mount(Button, {
      props: { disabled: true },
      slots: { default: 'Submit' },
    });
    expect(wrapper.find('button').element.disabled).toBe(true);
  });
});
