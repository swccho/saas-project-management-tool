<template>
  <Card>
    <CardHeader>
      <h2 class="text-lg font-semibold">Audit Logs</h2>
      <p class="text-sm text-gray-600">View administrative activity and changes.</p>
    </CardHeader>
    <CardContent>
      <div class="mb-4 flex flex-wrap gap-2">
        <Select
          v-model="filters.action_type"
          :options="actionTypeOptions"
          placeholder="All actions"
          class="w-48"
        />
        <Button variant="secondary" size="sm" @click="fetchLogs">Apply</Button>
      </div>
      <div v-if="loading" class="space-y-2">
        <div v-for="i in 5" :key="i" class="h-12 animate-pulse rounded bg-gray-100" />
      </div>
      <EmptyState
        v-else-if="logs.length === 0"
        title="No audit logs yet"
        :compact="true"
        :icon="FileText"
      />
      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="border-b border-gray-200">
              <th class="pb-3 font-medium text-gray-700">Actor</th>
              <th class="pb-3 font-medium text-gray-700">Action</th>
              <th class="pb-3 font-medium text-gray-700">Summary</th>
              <th class="pb-3 font-medium text-gray-700">Time</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="log in logs"
              :key="log.id"
              class="border-b border-gray-100"
            >
              <td class="py-3 text-gray-600">{{ log.actor?.name ?? 'System' }}</td>
              <td class="py-3">
                <span class="rounded bg-gray-100 px-2 py-0.5 text-xs font-medium">
                  {{ formatActionType(log.action_type) }}
                </span>
              </td>
              <td class="py-3 text-gray-900">{{ log.summary }}</td>
              <td class="py-3 text-gray-500">{{ formatDate(log.created_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="meta.last_page > 1" class="mt-4 flex justify-center gap-2">
        <Button
          variant="secondary"
          size="sm"
          :disabled="meta.current_page <= 1"
          @click="goToPage(meta.current_page - 1)"
        >
          Previous
        </Button>
        <span class="flex items-center px-2 text-sm text-gray-600">
          Page {{ meta.current_page }} of {{ meta.last_page }}
        </span>
        <Button
          variant="secondary"
          size="sm"
          :disabled="meta.current_page >= meta.last_page"
          @click="goToPage(meta.current_page + 1)"
        >
          Next
        </Button>
      </div>
    </CardContent>
  </Card>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { FileText } from 'lucide-vue-next';
import { useWorkspaceStore } from '../../../stores/workspaceStore';
import { auditLogService } from '../../../services/auditLogService';
import Card from '../../../components/ui/Card.vue';
import CardHeader from '../../../components/ui/CardHeader.vue';
import CardContent from '../../../components/ui/CardContent.vue';
import EmptyState from '../../../components/shared/EmptyState.vue';
import Select from '../../../components/ui/Select.vue';
import Button from '../../../components/ui/Button.vue';

const workspaceStore = useWorkspaceStore();

const logs = ref([]);
const meta = reactive({ current_page: 1, last_page: 1, total: 0 });
const loading = ref(false);
const filters = reactive({ action_type: '' });

const actionTypeOptions = [
  { value: '', label: 'All actions' },
  { value: 'workspace_updated', label: 'Workspace updated' },
  { value: 'member_removed', label: 'Member removed' },
  { value: 'member_role_changed', label: 'Role changed' },
  { value: 'member_invited', label: 'Member invited' },
  { value: 'invitation_revoked', label: 'Invitation revoked' },
  { value: 'invitation_accepted', label: 'Invitation accepted' },
  { value: 'owner_transferred', label: 'Owner transferred' },
  { value: 'preferences_updated', label: 'Preferences updated' },
  { value: 'branding_updated', label: 'Branding updated' },
];

function formatActionType(type) {
  return type?.replace(/_/g, ' ') ?? type;
}

function formatDate(iso) {
  if (!iso) return '';
  const d = new Date(iso);
  return d.toLocaleString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}

async function fetchLogs(page = 1) {
  const ws = workspaceStore.activeWorkspace;
  if (!ws) return;
  loading.value = true;
  try {
    const params = { page };
    if (filters.action_type) params.action_type = filters.action_type;
    const result = await auditLogService.list(ws.id, params);
    logs.value = result.data ?? [];
    Object.assign(meta, result.meta ?? {});
  } catch {
    logs.value = [];
  } finally {
    loading.value = false;
  }
}

function goToPage(p) {
  fetchLogs(p);
}

onMounted(() => fetchLogs());
watch(() => workspaceStore.activeWorkspaceId, () => fetchLogs());
</script>
