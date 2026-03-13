<template>
  <div class="space-y-6">
    <Card v-if="isOwner" class="border-amber-200">
      <CardHeader>
        <h2 class="text-lg font-semibold text-amber-800">Transfer ownership</h2>
        <p class="text-sm text-gray-600">Transfer this workspace to another member. You will become an admin.</p>
      </CardHeader>
      <CardContent>
        <Button variant="secondary" @click="showTransferModal = true">
          Transfer ownership
        </Button>
      </CardContent>
    </Card>

    <Card class="border-red-200">
      <CardHeader>
        <h2 class="text-lg font-semibold text-red-700">Danger zone</h2>
        <p class="text-sm text-gray-600">Permanently delete this workspace and all its data.</p>
      </CardHeader>
      <CardContent>
        <Button variant="destructive" @click="showDeleteConfirm = true">
          Delete workspace
        </Button>
      </CardContent>
    </Card>
  </div>

  <Modal v-model="showTransferModal">
    <div class="space-y-4">
      <h3 class="text-lg font-semibold">Transfer ownership</h3>
      <p class="text-sm text-gray-600">
        Select a member to become the new owner. You will become an admin.
      </p>
      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">New owner</label>
        <Select
          v-model="transferNewOwnerId"
          :options="transferOwnerOptions"
          placeholder="Select member"
        />
      </div>
      <p class="text-sm text-gray-600">
        Type <strong>{{ workspaceStore.activeWorkspace?.name }}</strong> to confirm.
      </p>
      <Input
        v-model="transferConfirmText"
        placeholder="Type workspace name to confirm"
      />
      <div class="flex justify-end gap-2">
        <Button variant="secondary" @click="showTransferModal = false">Cancel</Button>
        <Button
          :loading="transferring"
          :disabled="!transferNewOwnerId || transferConfirmText !== workspaceStore.activeWorkspace?.name"
          @click="transferOwnership"
        >
          Transfer ownership
        </Button>
      </div>
    </div>
  </Modal>

  <Modal v-model="showDeleteConfirm">
    <div class="space-y-4">
      <h3 class="text-lg font-semibold">Delete workspace?</h3>
      <p class="text-sm text-gray-600">
        This will permanently delete "{{ workspaceStore.activeWorkspace?.name }}" and all its data. This action cannot be undone.
      </p>
      <p class="text-sm text-gray-600">
        Type <strong>{{ workspaceStore.activeWorkspace?.name }}</strong> to confirm.
      </p>
      <Input
        v-model="deleteConfirmText"
        placeholder="Type workspace name to confirm"
      />
      <div class="flex justify-end gap-2">
        <Button variant="secondary" @click="showDeleteConfirm = false">Cancel</Button>
        <Button
          variant="destructive"
          :loading="deleting"
          :disabled="deleteConfirmText !== workspaceStore.activeWorkspace?.name"
          @click="deleteWorkspace"
        >
          Delete workspace
        </Button>
      </div>
    </div>
  </Modal>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useWorkspaceStore } from '../../../stores/workspaceStore';
import { useAuthStore } from '../../../stores/authStore';
import { workspaceService } from '../../../services/workspaceService';
import Card from '../../../components/ui/Card.vue';
import CardHeader from '../../../components/ui/CardHeader.vue';
import CardContent from '../../../components/ui/CardContent.vue';
import Input from '../../../components/ui/Input.vue';
import Select from '../../../components/ui/Select.vue';
import Button from '../../../components/ui/Button.vue';
import Modal from '../../../components/ui/Modal.vue';

const workspaceStore = useWorkspaceStore();
const authStore = useAuthStore();
const router = useRouter();

const members = ref([]);
const showTransferModal = ref(false);
const transferNewOwnerId = ref('');
const transferConfirmText = ref('');
const transferring = ref(false);
const showDeleteConfirm = ref(false);
const deleteConfirmText = ref('');
const deleting = ref(false);

const isOwner = computed(() => workspaceStore.activeWorkspace?.owner_id === authStore.user?.id);

const transferOwnerOptions = computed(() =>
  members.value
    .filter((m) => m.user_id !== authStore.user?.id && m.role !== 'owner')
    .map((m) => ({ value: String(m.user_id), label: `${m.user?.name} (${m.user?.email})` }))
);

async function fetchMembers() {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  try {
    members.value = await workspaceService.getMembers(ws.id) ?? [];
  } catch {
    members.value = [];
  }
}

async function transferOwnership() {
  const ws = workspaceStore.activeWorkspace;
  if (!ws || !transferNewOwnerId.value || transferConfirmText.value !== ws.name) return;
  transferring.value = true;
  try {
    await workspaceService.transferOwnership(ws.id, parseInt(transferNewOwnerId.value, 10));
    showTransferModal.value = false;
    transferNewOwnerId.value = '';
    transferConfirmText.value = '';
    await workspaceStore.fetchWorkspaces();
  } finally {
    transferring.value = false;
  }
}

onMounted(fetchMembers);
watch(() => workspaceStore.activeWorkspaceId, fetchMembers);

async function deleteWorkspace() {
  const ws = workspaceStore.activeWorkspace;
  if (!ws || deleteConfirmText.value !== ws.name) return;
  deleting.value = true;
  try {
    await workspaceStore.deleteWorkspace(ws.id);
    showDeleteConfirm.value = false;
    deleteConfirmText.value = '';
    router.push('/');
  } catch (e) {
    // Error shown via store or could add local state
  } finally {
    deleting.value = false;
  }
}
</script>
