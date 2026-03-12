<template>
  <div class="flex min-w-max flex-nowrap gap-4 overflow-x-auto pb-4">
    <div
      v-for="i in columnCount"
      :key="i"
      class="min-w-[240px] flex-shrink-0 rounded-xl border border-gray-200 bg-gray-50/50 p-4 sm:min-w-[280px]"
    >
      <div class="h-5 w-24 animate-pulse rounded bg-gray-200" />
      <div class="mt-4 space-y-2">
        <div
          v-for="j in cardCount"
          :key="j"
          class="h-20 animate-pulse rounded-lg bg-gray-200"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  columnCount: { type: Number, default: 4 },
  cardCount: { type: Number, default: 3 },
});
</script>
