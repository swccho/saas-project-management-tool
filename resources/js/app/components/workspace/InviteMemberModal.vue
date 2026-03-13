<template>
  <Modal :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)">
    <div class="space-y-4">
      <h3 class="text-lg font-semibold">Invite member</h3>
      <p class="text-sm text-gray-600">Send an invitation to join this workspace.</p>
      <form @submit.prevent="submit">
        <div class="space-y-4">
          <Input
            v-model="form.email"
            type="email"
            label="Email"
            placeholder="colleague@example.com"
            required
            :error="error"
          />
          <div class="space-y-1">
            <label class="block text-sm font-medium text-gray-700">Role</label>
            <Select
              v-model="form.role"
              :options="roleOptions"
              placeholder="Select role"
            />
            <p class="text-xs text-gray-500">Member can view and contribute. Admin can manage members and settings.</p>
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
          <Button type="button" variant="secondary" @click="$emit('update:modelValue', false)">Cancel</Button>
          <Button type="submit" :loading="loading">Send invitation</Button>
        </div>
      </form>
    </div>
  </Modal>
</template>

<script setup>
import { reactive, watch } from 'vue';
import Input from '../ui/Input.vue';
import Select from '../ui/Select.vue';
import Button from '../ui/Button.vue';
import Modal from '../ui/Modal.vue';

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  error: { type: String, default: '' },
  loading: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue', 'invite']);

const form = reactive({ email: '', role: 'member' });

const roleOptions = [
  { value: 'member', label: 'Member' },
  { value: 'admin', label: 'Admin' },
];

watch(() => props.modelValue, (v) => {
  if (!v) {
    form.email = '';
    form.role = 'member';
  }
});

function submit() {
  emit('invite', { email: form.email, role: form.role });
}
</script>
