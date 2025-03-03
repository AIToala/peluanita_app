<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <tr
        :class="
            cn(
                'border-b transition-colors hover:bg-neutral-100/50 data-[state=selected]:bg-neutral-100 dark:hover:bg-emerald-400/50 dark:data-[state=selected]:bg-emerald-800',
                props.class,
            )
        "
    >
        <slot />
    </tr>
</template>
