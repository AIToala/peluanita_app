<script setup lang="ts">
import { cn } from '@/lib/utils';
import type { PrimitiveProps } from 'reka-ui';
import { Primitive } from 'reka-ui';
import type { HTMLAttributes } from 'vue';

const props = withDefaults(
    defineProps<
        PrimitiveProps & {
            size?: 'sm' | 'md';
            isActive?: boolean;
            class?: HTMLAttributes['class'];
        }
    >(),
    {
        as: 'a',
        size: 'md',
    },
);
</script>

<template>
    <Primitive
        data-sidebar="menu-sub-button"
        :as="as"
        :as-child="asChild"
        :data-size="size"
        :data-active="isActive"
        :class="
            cn(
                'dark:bg-gray-900-accent dark:bg-gray-900-accent flex h-7 min-w-0 -translate-x-px items-center gap-2 overflow-hidden rounded-md px-2 text-[#40404a] outline-none ring-sidebar-ring hover:bg-[#fafafa] hover:text-sidebar-accent-foreground focus-visible:ring-2 active:bg-[#fafafa] active:text-sidebar-accent-foreground disabled:pointer-events-none disabled:opacity-50 aria-disabled:pointer-events-none aria-disabled:opacity-50 dark:text-neutral-50 [&>span:last-child]:truncate [&>svg]:size-4 [&>svg]:shrink-0 [&>svg]:text-sidebar-accent-foreground',
                'dark:bg-gray-900-accent data-[active=true]:bg-[#fafafa] data-[active=true]:text-sidebar-accent-foreground',
                size === 'sm' && 'text-xs',
                size === 'md' && 'text-sm',
                'group-data-[collapsible=icon]:hidden',
                props.class,
            )
        "
    >
        <slot />
    </Primitive>
</template>
