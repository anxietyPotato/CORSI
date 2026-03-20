<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    stats: Object,
});

const stats = ref(props.stats);
let interval = null;

async function fetchLive() {
    try {
        const response = await fetch('/statistics/live');
        const data = await response.json();
        stats.value = data;
    } catch (e) {
        console.error('Failed to fetch live stats', e);
    }
}

onMounted(() => {
    interval = setInterval(fetchLive, 5000);
});

onUnmounted(() => {
    clearInterval(interval);
});
</script>

<template>
    <Head title="Statistics" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                📊 Statistics Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">

                <!-- Summary Cards -->
                <div class="grid grid-cols-3 gap-6">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">
                        <p class="text-4xl font-bold text-indigo-600">{{ stats.total_workshops }}</p>
                        <p class="text-gray-600 mt-2">Total Workshops</p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">
                        <p class="text-4xl font-bold text-green-600">{{ stats.total_registrations }}</p>
                        <p class="text-gray-600 mt-2">Total Registrations</p>
                        <p class="text-xs text-gray-400 mt-1">Updates every 5 seconds 🔄</p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">
                        <p class="text-2xl font-bold text-yellow-600">
                            {{ stats.most_popular ? stats.most_popular.title : 'N/A' }}
                        </p>
                        <p class="text-gray-600 mt-2">Most Popular Workshop</p>
                        <p v-if="stats.most_popular" class="text-xs text-gray-400 mt-1">
                            {{ stats.most_popular.confirmed_count }} / {{ stats.most_popular.capacity }} seats taken
                        </p>
                    </div>
                </div>

                <!-- Workshop Breakdown -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Workshop Registrations</h3>
                    <table class="w-full text-left">
                        <thead>
                        <tr class="border-b">
                            <th class="pb-3 text-gray-600">Workshop</th>
                            <th class="pb-3 text-gray-600 text-center">Confirmed</th>
                            <th class="pb-3 text-gray-600 text-center">Waiting</th>
                            <th class="pb-3 text-gray-600 text-center">Capacity</th>
                            <th class="pb-3 text-gray-600 text-center">Fill Rate</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr
                            v-for="workshop in stats.workshops"
                            :key="workshop.id"
                            class="border-b hover:bg-gray-50"
                        >
                            <td class="py-3 font-medium">{{ workshop.title }}</td>
                            <td class="py-3 text-center text-green-600 font-bold">{{ workshop.confirmed_count }}</td>
                            <td class="py-3 text-center text-yellow-600 font-bold">{{ workshop.waiting_count }}</td>
                            <td class="py-3 text-center text-gray-600">{{ workshop.capacity }}</td>
                            <td class="py-3 text-center">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div
                                        class="bg-indigo-600 h-2 rounded-full"
                                        :style="`width: ${Math.min((workshop.confirmed_count / workshop.capacity) * 100, 100)}%`"
                                    ></div>
                                </div>
                                <span class="text-xs text-gray-500">
                                        {{ Math.round((workshop.confirmed_count / workshop.capacity) * 100) }}%
                                    </span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
