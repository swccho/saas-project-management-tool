<template>
  <div class="relative">
    <textarea
      ref="textareaRef"
      :value="modelValue"
      :placeholder="placeholder"
      :rows="rows"
      :disabled="disabled"
      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 disabled:bg-gray-50 disabled:text-gray-500"
      @input="onInput"
      @keydown="onKeydown"
    />
    <Teleport v-if="showDropdown" to="body">
      <div
        class="fixed z-[100] max-h-48 overflow-auto rounded-lg border border-gray-200 bg-white py-1 shadow-lg"
        :style="dropdownStyle"
      >
        <button
          v-for="(m, i) in filteredMembers"
          :key="m.user_id"
          type="button"
          :class="[
            'flex w-full items-center gap-2 px-3 py-2 text-left text-sm hover:bg-indigo-50',
            i === selectedIndex && 'bg-indigo-50',
          ]"
          @click="selectMember(m)"
        >
          <Avatar v-if="m.user" :name="m.user.name" size="xs" />
          <span>{{ m.user?.name ?? 'Unknown' }}</span>
        </button>
        <div v-if="filteredMembers.length === 0" class="px-3 py-2 text-sm text-gray-500">
          No members match
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import Avatar from '../ui/Avatar.vue';

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  rows: { type: Number, default: 3 },
  disabled: { type: Boolean, default: false },
  members: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:modelValue']);

const textareaRef = ref(null);
const showDropdown = ref(false);
const mentionStart = ref(-1);
const mentionQuery = ref('');
const selectedIndex = ref(0);
const dropdownPosition = ref({ top: 0, left: 0 });

const memberUsers = computed(() =>
  props.members
    .map((m) => (m.user ? { user_id: m.user_id, name: m.user.name } : null))
    .filter(Boolean)
);

const filteredMembers = computed(() => {
  const q = mentionQuery.value.toLowerCase().trim();
  if (!q) return props.members.slice(0, 8);
  return props.members.filter((m) =>
    (m.user?.name ?? '').toLowerCase().includes(q)
  ).slice(0, 8);
});

const dropdownStyle = computed(() => ({
  top: `${dropdownPosition.value.top}px`,
  left: `${dropdownPosition.value.left}px`,
  minWidth: '200px',
}));

function getCaretCoordinates() {
  const el = textareaRef.value;
  if (!el) return { top: 0, left: 0 };
  const rect = el.getBoundingClientRect();
  const style = getComputedStyle(el);
  const lineHeight = parseInt(style.lineHeight, 10) || 20;
  const paddingTop = parseInt(style.paddingTop, 10) || 0;
  const paddingLeft = parseInt(style.paddingLeft, 10) || 0;
  const { selectionStart } = el;
  const textBeforeCaret = el.value.substring(0, selectionStart);
  const lines = textBeforeCaret.split('\n');
  const lineIndex = lines.length - 1;
  const colIndex = lines[lines.length - 1].length;
  const top = rect.top + paddingTop + (lineIndex + 1) * lineHeight;
  const left = rect.left + paddingLeft;
  return { top, left };
}

function onInput(e) {
  const val = e.target.value;
  emit('update:modelValue', val);
  const cursorPos = e.target.selectionStart;
  const textBefore = val.substring(0, cursorPos);
  const atMatch = textBefore.match(/@([^\s@]*)$/);
  if (atMatch) {
    mentionStart.value = cursorPos - atMatch[0].length;
    mentionQuery.value = atMatch[1];
    showDropdown.value = true;
    selectedIndex.value = 0;
    dropdownPosition.value = getCaretCoordinates();
  } else {
    showDropdown.value = false;
  }
}

function onKeydown(e) {
  if (!showDropdown.value) return;
  if (e.key === 'ArrowDown') {
    e.preventDefault();
    selectedIndex.value = Math.min(selectedIndex.value + 1, filteredMembers.value.length - 1);
    return;
  }
  if (e.key === 'ArrowUp') {
    e.preventDefault();
    selectedIndex.value = Math.max(selectedIndex.value - 1, 0);
    return;
  }
  if (e.key === 'Enter' && filteredMembers.value.length > 0) {
    e.preventDefault();
    selectMember(filteredMembers.value[selectedIndex.value]);
    return;
  }
  if (e.key === 'Escape') {
    showDropdown.value = false;
  }
}

function selectMember(m) {
  const name = m.user?.name ?? 'Unknown';
  const val = props.modelValue;
  const before = val.substring(0, mentionStart.value);
  const after = val.substring(textareaRef.value?.selectionStart ?? val.length);
  const newVal = `${before}@${name} ${after}`;
  emit('update:modelValue', newVal);
  showDropdown.value = false;
  nextTick(() => {
    textareaRef.value?.focus();
    const pos = before.length + name.length + 2;
    textareaRef.value?.setSelectionRange(pos, pos);
  });
}

watch(showDropdown, (v) => {
  if (v) selectedIndex.value = 0;
});
</script>
