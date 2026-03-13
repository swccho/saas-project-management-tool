import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import CreateProjectModal from '../../resources/js/app/components/projects/CreateProjectModal.vue';

describe('CreateProjectModal', () => {
  it('renders modal with name and description fields', () => {
    const wrapper = mount(CreateProjectModal, {
      props: { modelValue: true },
    });
    expect(wrapper.find('input').exists()).toBe(true);
    expect(wrapper.text()).toMatch(/project|name|description/i);
  });

  it('emits submit with form data when form is submitted', async () => {
    const wrapper = mount(CreateProjectModal, {
      props: { modelValue: true },
    });
    const inputs = wrapper.findAll('input');
    const nameInput = inputs.find((i) => i.attributes('placeholder') === 'My Project') || inputs[0];
    if (nameInput) {
      await nameInput.setValue('Test Project');
    }
    await wrapper.find('form').trigger('submit.prevent');
    expect(wrapper.emitted('submit')).toBeTruthy();
    const payload = wrapper.emitted('submit')[0][0];
    expect(payload.name).toBe('Test Project');
  });
});
