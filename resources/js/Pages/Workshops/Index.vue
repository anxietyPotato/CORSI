<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    workshops: Array,
    auth: Object,
});

const isAdmin = computed(() => props.auth.user.role === 'admin');

function register(workshopId) {
    router.post(`/workshops/${workshopId}/register`);
}

function unregister(workshopId) {
    router.delete(`/workshops/${workshopId}/register`);
}

function deleteWorkshop(workshopId) {
    if (confirm('Are you sure you want to delete this workshop?')) {
        router.delete(`/workshops/${workshopId}`);
    }
}

function isRegistered(workshop) {
    return workshop.confirmed_registrations?.some(r => r.user_id === props.auth.user.id);
}

function isWaiting(workshop) {
    return workshop.waiting_list?.some(r => r.user_id === props.auth.user.id);
}

function availableSeats(workshop) {
    return workshop.capacity - (workshop.confirmed_registrations?.length ?? 0);
}
</script>

<template>
    <Head title="Workshops" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Workshops
                </h2>
                <Link
                    v-if="isAdmin"
                    :href="route('workshops.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                >
                    + New Workshop
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-4">

                <div v-if="workshops.length === 0" class="text-center text-gray-500 py-12">
                    No workshops available yet.
                </div>

                <div
                    v-for="workshop in workshops"
                    :key="workshop.id"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ workshop.title }}</h3>
                            <p class="text-gray-600 mt-1">{{ workshop.description }}</p>
                            <div class="mt-2 text-sm text-gray-500 space-y-1">
                                <p>📅 {{ new Date(workshop.starts_at).toLocaleString() }} → {{ new Date(workshop.ends_at).toLocaleString() }}</p>
                                <p>🪑 Available seats: {{ availableSeats(workshop) }} / {{ workshop.capacity }}</p>
                            </div>
                        </div>

                        <!-- Admin actions -->
                        <div v-if="isAdmin" class="flex gap-2">
                            <Link
                                :href="route('workshops.edit', workshop.id)"
                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm"
                            >
                                Edit
                            </Link>
                            <button
                                @click="deleteWorkshop(workshop.id)"
                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                            >
                                Delete
                            </button>
                        </div>

                        <!-- Employee actions -->
                        <div v-else>
                            <span v-if="isRegistered(workshop)" class="text-green-600 font-medium mr-2">✅ Registered</span>
                            <span v-else-if="isWaiting(workshop)" class="text-yellow-600 font-medium mr-2">⏳ Waiting List</span>

                            <button
                                v-if="!isRegistered(workshop) && !isWaiting(workshop)"
                                @click="register(workshop.id)"
                                class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm"
                            >
                                {{ availableSeats(workshop) > 0 ? 'Register' : 'Join Waiting List' }}
                            </button>

                            <button
                                v-else
                                @click="unregister(workshop.id)"
                                class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-sm"
                            >
                                Cancel Registration
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
