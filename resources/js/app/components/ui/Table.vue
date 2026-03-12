<template>
  <div class="overflow-x-auto rounded-lg border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th
            v-for="col in columns"
            :key="col.key"
            class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
          >
            {{ col.label }}
          </th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 bg-white">
        <tr v-for="(row, i) in data" :key="i" class="hover:bg-gray-50">
          <td
            v-for="col in columns"
            :key="col.key"
            class="whitespace-nowrap px-6 py-4 text-sm text-gray-900"
          >
            <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
              {{ row[col.key] }}
            </slot>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  columns: { type: Array, required: true },
  data: { type: Array, default: () => [] },
});
</script>
