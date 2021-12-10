<template>
    <span class="-mr-2"><span v-for="value of field.value" :key="value" class="rounded-full px-2 py-1 whitespace-no-wrap text-xs font-bold text-xs uppercase bg-gray-200 mr-2">{{ value }}</span></span>
</template>

<script>
export default {
    props: ['resourceName', 'field'],
}
</script>
