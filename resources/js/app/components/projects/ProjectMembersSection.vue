<template>
  <Card>
    <CardHeader>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold">Members</h2>
          <p class="text-sm text-gray-600">Manage who has access to this project.</p>
        </div>
        <Button size="sm" @click="showAdd = true">Add member</Button>
      </div>
    </CardHeader>
    <CardContent>
      <div v-if="loading" class="space-y-3">
        <div v-for="i in 3" :key="i" class="h-12 animate-pulse rounded bg-gray-100" />
      </div>
      <ul v-else class="divide-y divide-gray-200">
        <li
          v-for="m in members"
          :key="m.id"
          class="flex items-center justify-between py-3"
        >
          <div class="flex items-center gap-3">
            <Avatar :name="m.user?.name" size="sm" />
            <div>
              <p class="text-sm font-medium text-gray-900">{{ m.user?.name }}</p>
              <p class="text-xs text-gray-500">{{ m.user?.email }}</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <Select
              :model-value="m.role"
              :options="roleOptions"
              @update:model-value="(v) => updateRole(m, v)"
            />
            <Button variant="ghost" size="sm" @click="confirmRemove(m)">Remove</Button>
          </div>
        </li>
      </ul>
      <p v-if="!loading && members.length === 0" class="py-4 text-center text-sm text-gray-500">
        No members yet. Add workspace members to the project.
      </p>

      <div
        v-if="showAdd"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click.self="showAdd = false"
      >
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
          <h3 class="text-lg font-semibold">Add member</h3>
          <p class="mt-1 text-sm text-gray-600">Select a workspace member to add.</p>
          <div class="mt-4 space-y-2">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search by name or email..."
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm"
            />
            <div class="max-h-48 overflow-y-auto">
              <button
                v-for="wm in filteredWorkspaceMembers"
                :key="wm.user_id"
                type="button"
                :disabled="isAlreadyMember(wm.user_id)"
                class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-left text-sm hover:bg-gray-50 disabled:opacity-50"
                @click="addMember(wm)"
              >
                <Avatar :name="wm.user?.name" size="sm" />
                <span>{{ wm.user?.name }}</span>
                <span class="text-gray-500">({{ wm.user?.email }})</span>
              </button>
            </div>
          </div>
          <div class="mt-4 flex justify-end">
            <Button variant="secondary" @click="showAdd = false">Cancel</Button>
          </div>
        </div>
      </div>

      <Modal v-model="showRemoveConfirm">
        <div class="space-y-4">
          <h3 class="text-lg font-semibold">Remove member?</h3>
          <p class="text-sm text-gray-600">
            {{ memberToRemove?.user?.name }} will lose access to this project.
          </p>
          <div class="flex justify-end gap-2">
            <Button variant="secondary" @click="showRemoveConfirm = false">Cancel</Button>
            <Button variant="destructive" :loading="removing" @click="removeMember">
              Remove
            </Button>
          </div>
        </div>
      </Modal>
    </CardContent>
  </Card>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { workspaceService } from '../../services/workspaceService';
import { projectMemberService } from '../../services/projectMemberService';
import Card from '../ui/Card.vue';
import CardHeader from '../ui/CardHeader.vue';
import CardContent from '../ui/CardContent.vue';
import Button from '../ui/Button.vue';
import Avatar from '../ui/Avatar.vue';
import Select from '../ui/Select.vue';
import Modal from '../ui/Modal.vue';

const props = defineProps({
  workspaceId: { type: Number, required: true },
  projectId: { type: Number, required: true },
});

const members = ref([]);
const workspaceMembers = ref([]);
const loading = ref(false);
const showAdd = ref(false);
const searchQuery = ref('');
const showRemoveConfirm = ref(false);
const memberToRemove = ref(null);
const removing = ref(false);

const roleOptions = [
  { value: 'project_admin', label: 'Admin' },
  { value: 'project_member', label: 'Member' },
  { value: 'project_viewer', label: 'Viewer' },
];

const filteredWorkspaceMembers = computed(() => {
  const q = searchQuery.value.toLowerCase();
  if (!q) return workspaceMembers.value;
  return workspaceMembers.value.filter(
    (wm) =>
      wm.user?.name?.toLowerCase().includes(q) ||
      wm.user?.email?.toLowerCase().includes(q)
  );
});

function isAlreadyMember(userId) {
  return members.value.some((m) => m.user_id === userId);
}

async function fetchMembers() {
  loading.value = true;
  try {
    const data = await projectMemberService.list(props.workspaceId, props.projectId);
    members.value = Array.isArray(data) ? data : (data?.data ?? []);
  } finally {
    loading.value = false;
  }
}

async function fetchWorkspaceMembers() {
  const data = await workspaceService.getMembers(props.workspaceId);
  workspaceMembers.value = Array.isArray(data) ? data : (data?.data ?? []);
}

async function addMember(wm) {
  try {
    await projectMemberService.add(props.workspaceId, props.projectId, {
      user_id: wm.user_id,
      role: 'project_member',
    });
    showAdd.value = false;
    await fetchMembers();
  } catch (e) {
    // Handle error
  }
}

function confirmRemove(m) {
  memberToRemove.value = m;
  showRemoveConfirm.value = true;
}

async function removeMember() {
  if (!memberToRemove.value) return;
  removing.value = true;
  try {
    await projectMemberService.remove(
      props.workspaceId,
      props.projectId,
      memberToRemove.value.id
    );
    showRemoveConfirm.value = false;
    memberToRemove.value = null;
    await fetchMembers();
  } finally {
    removing.value = false;
  }
}

async function updateRole(m, role) {
  try {
    await projectMemberService.update(props.workspaceId, props.projectId, m.id, { role });
    m.role = role;
  } catch {
    // Revert on error
  }
}

watch([() => props.workspaceId, () => props.projectId], () => {
  fetchMembers();
  fetchWorkspaceMembers();
}, { immediate: true });

watch(showAdd, (open) => {
  if (open) fetchWorkspaceMembers();
});
</script>
