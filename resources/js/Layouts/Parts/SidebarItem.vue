<script lang="ts" setup>
import {Link} from '@inertiajs/vue3';
import {computed, ref} from 'vue';
import ChevronDownIcon from '@/Icons/ChevronDownIcon.vue';
import {SidebarMenuItem} from '@/types/component';

const props = defineProps<{
    item: SidebarMenuItem;
}>();

const showDropdown = ref(false);

const currentPath = computed(() => {
    return window.location.pathname;
});

if (props.item.children && props.item.children.length > 0) {
    props.item.children.forEach((child) => {
        if (child.url) {
            const childPath = new URL(child.url).pathname;
            if (childPath === currentPath.value) {
                child.active = true;
                showDropdown.value = true;
            }
        }
    });
} else if (props.item.url) {
    const itemPath = new URL(props.item.url).pathname;
    if (itemPath === currentPath.value) {
        props.item.active = true;
    }
}
</script>

<template>
    <button
        v-if="item.children && item.children.length > 0"
        :class="{active: showDropdown}"
        class="sidebar-item"
        type="button"
        @click="showDropdown = !showDropdown"
    >
        <component :is="{...item.icon}" v-if="item.icon" height="18" width="18"></component>

        {{ item.label }}

        <ChevronDownIcon
            :class="{'rotate-180': showDropdown}"
            class="absolute right-4 top-1/2 size-4 -translate-y-1/2 transition-transform duration-300"
        />
    </button>
    <Link v-else-if="item.url" :class="{active: item.active}" :href="item.url" class="sidebar-item">
        <component :is="{...item.icon}" v-if="item.icon" height="18" width="18"></component>

        {{ item.label }}
    </Link>
    <span v-else class="sidebar-item">
        <component :is="{...item.icon}" v-if="item.icon" height="18" width="18"></component>

        {{ item.label }}
    </span>

    <div v-if="showDropdown" class="translate mt-2 transform space-y-2 overflow-hidden pl-5">
        <Link
            v-for="(child, index) in item.children"
            :key="index"
            :class="{active: child.active}"
            :href="child.url"
            class="sidebar-item"
        >
            <component :is="{...child.icon}" v-if="child.icon" height="18" width="18"></component>

            {{ child.label }}
        </Link>
    </div>
</template>
