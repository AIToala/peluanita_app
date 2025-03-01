<template>
    <div class="flex items-center space-x-2">
        <img :src="logoSource" :alt="altText" :class="classNames" />
        <span
            v-if="showText"
            class="font-regular font-serif text-xl italic text-gray-900 dark:text-white"
        >
            {{ brandSlogan }}
        </span>
    </div>
</template>

<script setup>
import logoFooter from '@/assets/images/logo/logo-no-background.png';
import logoImage from '@/assets/images/logo/logo.png';
import { computed, defineProps } from 'vue';
// Props for customization
const props = defineProps({
    logoSrc: {
        type: String,
        default: '',
    },
    altText: {
        type: String,
        default: 'Logo',
    },
    brandSlogan: {
        type: String,
        default: 'Transformando tu look, inspirando confianza',
    },
    showText: {
        type: Boolean,
        default: false,
    },
    classNames: {
        type: String,
        default: 'w-42 h-24 object-contain',
    },
});

// Computed property to handle the logo source with fallback
const logoSource = computed(() => {
    return props.logoSrc == 'footer' ? logoFooter : logoImage;
});
</script>
