<script setup lang="ts">
import { Separator } from '@/components/ui/separator';
import { cn } from '@/lib/utils';
import type { HTMLAttributes } from 'vue';

const props = defineProps<{
    class?: HTMLAttributes['class'];
}>();
</script>

<template>
    <Separator
        data-sidebar="separator"
        :class="
            cn('dark:bg-gray-900-border mx-2 w-auto bg-[#fafafa]', props.class)
        "
    >
        <slot />
    </Separator>
</template>
