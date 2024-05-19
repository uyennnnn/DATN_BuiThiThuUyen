<template>
  <div class="grid grid-cols-3 text-sm">
    <div class="col-span-1 text-center border border-gray-200 py-2.5">
      {{ label }}
    </div>
    <div
      class="col-span-2 flex items-center justify-center gap-2 border border-gray-200 bg-white py-2.5 font-semibold"
      :style="{ color: textColor }"
    >
      <slot></slot>
    </div>
  </div>
</template>
  
  <script>
export default {
  props: {
    label: String,
    content: String,
    textColor: String,
  },
};
</script>
  