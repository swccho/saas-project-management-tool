<template>
  <Card>
    <CardHeader>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-lg font-semibold">Members</h2>
          <p class="text-sm text-gray-600">Manage workspace members and their roles.</p>
        </div>
        <Button v-if="canManage" @click="showInvite = true">Invite member</Button>
      </div>
    </CardHeader>
    <CardContent>
      <div v-if="invitationsLoading" class="mb-4 space-y-2">
        <div class="h-10 animate-pulse rounded bg-gray-100" />
      </div>
      <div v-else-if="invitations.length > 0" class="mb-6">
        <h3 class="mb-2 text-sm font-medium text-gray-700">Pending invitations</h3>
        <ul class="space-y-2 rounded-lg border border-gray-200 p-3">
          <li
            v-for="i in invitations"
            :key="i.id"
            class="flex items-center justify-between text-sm"
          >
            <span class="text-gray-900">{{ i.email }}</span>
            <span class="text-gray-500">({{ roleLabel(i.role) }})</span>
            <div class="flex gap-2">
              <Button variant="ghost" size="sm" @click="resendInvitation(i)">Resend</Button>
              <Button variant="ghost" size="sm" class="text-red-600" @click="revokeInvitation(i)">Revoke</Button>
            </div>
          </li>
        </ul>
      </div>
      <div v-if="loading" class="space-y-3">
        <div v-for="i in 3" :key="i" class="h-12 animate-pulse rounded bg-gray-100" />
      </div>
      <EmptyState
        v-else-if="members.length === 0"
        title="No members yet"
        :compact="true"
        :icon="Users"
      />
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="border-b border-gray-200">
              <th class="pb-3 font-medium text-gray-700">Member</th>
              <th class="pb-3 font-medium text-gray-700">Role</th>
              <th class="pb-3 font-medium text-gray-700">Joined</th>
              <th v-if="canManage" class="pb-3 font-medium text-gray-700 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="m in members"
              :key="m.id"
              class="border-b border-gray-100"
            >
              <td class="py-3">
                <div class="flex items-center gap-3">
                  <Avatar :name="m.user?.name" size="sm" />
                  <div>
                    <p class="font-medium text-gray-900">{{ m.user?.name }}</p>
                    <p class="text-xs text-gray-500">{{ m.user?.email }}</p>
                  </div>
                </div>
              </td>
              <td class="py-3">
                <span
                  v-if="m.role === 'owner'"
                  class="inline-flex rounded-md bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800"
                >
                  Owner
                </span>
                <Select
                  v-else-if="canManage && canChangeRole(m)"
                  :model-value="m.role"
                  :options="roleOptionsForCurrentUser"
                  @update:model-value="(v) => updateRole(m, v)"
                />
                <span v-else class="text-gray-600">{{ roleLabel(m.role) }}</span>
              </td>
              <td class="py-3 text-gray-500">{{ formatDate(m.joined_at) }}</td>
              <td v-if="canManage" class="py-3 text-right">
                <Button
                  v-if="canRemove(m)"
                  variant="ghost"
                  size="sm"
                  class="text-red-600 hover:bg-red-50 hover:text-red-700"
                  @click="confirmRemove(m)"
                >
                  Remove
                </Button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <InviteMemberModal
        v-model="showInvite"
        :error="inviteError"
        :loading="inviting"
        @invite="handleInvite"
      />

      <Modal v-model="showRemoveConfirm">
        <div class="space-y-4">
          <h3 class="text-lg font-semibold">Remove member?</h3>
          <p class="text-sm text-gray-600">
            {{ memberToRemove?.user?.name }} will lose access to this workspace. This action cannot be undone.
          </p>
          <div class="flex justify-end gap-2">
            <Button variant="secondary" @click="showRemoveConfirm = false">Cancel</Button>
            <Button
              variant="destructive"
              :loading="removing"
              @click="removeMember"
            >
              Remove
            </Button>
          </div>
        </div>
      </Modal>
    </CardContent>
  </Card>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Users } from 'lucide-vue-next';
import { useWorkspaceStore } from '../../../stores/workspaceStore';
import { useAuthStore } from '../../../stores/authStore';
import { workspaceService } from '../../../services/workspaceService';
import { invitationService } from '../../../services/invitationService';
import { showSuccessToast } from '../../../services/apiErrorHandler';
import Card from '../../../components/ui/Card.vue';
import EmptyState from '../../../components/shared/EmptyState.vue';
import InviteMemberModal from '../../../components/workspace/InviteMemberModal.vue';
import CardHeader from '../../../components/ui/CardHeader.vue';
import CardContent from '../../../components/ui/CardContent.vue';
import Avatar from '../../../components/ui/Avatar.vue';
import Select from '../../../components/ui/Select.vue';
import Button from '../../../components/ui/Button.vue';
import Modal from '../../../components/ui/Modal.vue';

const workspaceStore = useWorkspaceStore();
const authStore = useAuthStore();

const members = ref([]);
const loading = ref(false);
const invitations = ref([]);
const invitationsLoading = ref(false);
const showInvite = ref(false);
const inviteError = ref('');
const inviting = ref(false);
const showRemoveConfirm = ref(false);
const memberToRemove = ref(null);
const removing = ref(false);

const roleOptions = [
  { value: 'member', label: 'Member' },
  { value: 'admin', label: 'Admin' },
  { value: 'owner', label: 'Owner' },
];

const currentUserMember = computed(() =>
  members.value.find((m) => m.user_id === authStore.user?.id)
);

const roleOptionsForCurrentUser = computed(() => {
  const m = currentUserMember.value;
  if (!m || m.role === 'member') return [{ value: 'member', label: 'Member' }];
  if (m.role === 'admin') return [{ value: 'member', label: 'Member' }];
  return [
    { value: 'member', label: 'Member' },
    { value: 'admin', label: 'Admin' },
  ];
});

const canManage = computed(() => {
  const m = currentUserMember.value;
  return m && (m.role === 'owner' || m.role === 'admin');
});

function canChangeRole(m) {
  return m.role !== 'owner';
}

function canRemove(m) {
  if (m.role === 'owner') return false;
  if (m.user_id === authStore.user?.id) return false;
  return canManage.value;
}

function roleLabel(role) {
  return roleOptions.find((o) => o.value === role)?.label ?? role;
}

function formatDate(iso) {
  if (!iso) return '';
  const d = new Date(iso);
  return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
}

async function fetchMembers() {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  loading.value = true;
  try {
    members.value = await workspaceService.getMembers(ws.id) ?? [];
  } catch {
    members.value = [];
  } finally {
    loading.value = false;
  }
}

async function fetchInvitations() {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  invitationsLoading.value = true;
  try {
    invitations.value = await invitationService.list(ws.id) ?? [];
  } catch {
    invitations.value = [];
  } finally {
    invitationsLoading.value = false;
  }
}

async function handleInvite(payload) {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  inviteError.value = '';
  inviting.value = true;
  try {
    await invitationService.create(ws.id, payload);
    showInvite.value = false;
    showSuccessToast('Invitation sent successfully');
    await fetchInvitations();
  } catch (e) {
    inviteError.value = e.response?.data?.message ?? 'Failed to send invitation.';
  } finally {
    inviting.value = false;
  }
}

async function resendInvitation(inv) {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  try {
    await invitationService.resend(ws.id, inv.id);
    await fetchInvitations();
  } catch {
    // Error toast shown by apiClient interceptor
  }
}

async function revokeInvitation(inv) {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  try {
    await invitationService.revoke(ws.id, inv.id);
    invitations.value = invitations.value.filter((x) => x.id !== inv.id);
  } catch {
    // Error toast shown by apiClient interceptor
  }
}

async function updateRole(m, newRole) {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  try {
    const updated = await workspaceService.updateMemberRole(ws.id, m.id, newRole);
    const idx = members.value.findIndex((x) => x.id === m.id);
    if (idx >= 0) members.value[idx] = updated;
  } catch {
    // Error toast shown by apiClient interceptor
  }
}

function confirmRemove(m) {
  memberToRemove.value = m;
  showRemoveConfirm.value = true;
}

async function removeMember() {
  const ws = workspaceStore.activeWorkspace;
  const m = memberToRemove.value;
  if (!ws || !m) return;
  removing.value = true;
  try {
    await workspaceService.removeMember(ws.id, m.id);
    members.value = members.value.filter((x) => x.id !== m.id);
    showRemoveConfirm.value = false;
    memberToRemove.value = null;
  } catch {
    // Error toast shown by apiClient interceptor
  } finally {
    removing.value = false;
  }
}

onMounted(() => {
  fetchMembers();
  fetchInvitations();
});
watch(() => workspaceStore.activeWorkspaceId, () => {
  fetchMembers();
  fetchInvitations();
});
</script>
