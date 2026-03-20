<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    workshop: Object,
    auth: Object,
});

const isAdmin = computed(() => props.auth.user.role === 'admin');

const isRegistered = computed(() =>
    props.workshop.confirmed_registrations?.some(r => r.user_id === props.auth.user.id)
);

const isWaiting = computed(() =>
    props.workshop.waiting_list?.some(r => r.user_id === props.auth.user.id)
);

const availableSeats = computed(() =>
    props.workshop.capacity - (props.workshop.confirmed_registrations?.length ?? 0)
);

function register() {
    router.post(`/workshops/${props.workshop.id}/register`);
}

function unregister() {
    router.delete(`/workshops/${props.workshop.id}/register`);
}

function deleteWorkshop() {
    if (confirm('Are you sure you want to delete this workshop?')) {
        router.delete(`/workshops/${props.workshop.id}`);
    }
}
</script>

<template>
    <Head :title="workshop.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ workshop.title }}
                </h2>

                <!-- Admin actions -->
                <div v-if="isAdmin" class="flex gap-2">

                    <a :href="route('workshops.edit', workshop.id)"
                    class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-sm"
                    >
                    Edit
                    </a>
                    <button
                        @click="deleteWorkshop"
                        class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8 space-y-6">

                <!-- Workshop Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Workshop Details</h3>

                    <div class="space-y-3 text-gray-700">
                        <p>📝 <span class="font-medium">Description:</span> {{ workshop.description }}</p>
                        <p>📅 <span class="font-medium">Starts:</span> {{ new Date(workshop.starts_at).toLocaleString() }}</p>
                        <p>🏁 <span class="font-medium">Ends:</span> {{ new Date(workshop.ends_at).toLocaleString() }}</p>
                        <p>🪑 <span class="font-medium">Available Seats:</span> {{ availableSeats }} / {{ workshop.capacity }}</p>
                    </div>

                    <!-- Employee registration actions -->
                    <div v-if="!isAdmin" class="mt-6">
                        <span v-if="isRegistered" class="text-green-600 font-medium">✅ You are registered for this workshop!</span>
                        <span v-else-if="isWaiting" class="text-yellow-600 font-medium">⏳ You are on the waiting list.</span>

                        <div class="mt-3">
                            <button
                                v-if="!isRegistered && !isWaiting"
                                @click="register"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                            >
                                {{ availableSeats > 0 ? 'Register' : 'Join Waiting List' }}
                            </button>

                            <button
                                v-else
                                @click="unregister"
                                class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600"
                            >
                                Cancel Registration
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Confirmed Participants -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        Confirmed Participants ({{ workshop.confirmed_registrations?.length ?? 0 }})
                    </h3>

                    <div v-if="workshop.confirmed_registrations?.length === 0" class="text-gray-500">
                        No participants yet.
                    </div>

                    <ul v-else class="space-y-2">
                        <li
                            v-for="registration in workshop.confirmed_registrations"
                            :key="registration.id"
                            class="text-gray-700 border-b pb-2"
                        >
                            👤 {{ registration.user?.name ?? 'Unknown' }}
                        </li>
                    </ul>
                </div>

                <!-- Waiting List (Admin only) -->
                <div v-if="isAdmin" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        Waiting List ({{ workshop.waiting_list?.length ?? 0 }})
                    </h3>

                    <div v-if="workshop.waiting_list?.length === 0" class="text-gray-500">
                        No one on the waiting list.
                    </div>

                    <ul v-else class="space-y-2">
                        <li
                            v-for="(registration, index) in workshop.waiting_list"
                            :key="registration.id"
                            class="text-gray-700 border-b pb-2"
                        >
                            {{ index + 1 }}. 👤 {{ registration.user?.name ?? 'Unknown' }}
                        </li>
                    </ul>
                </div>

                <!-- Back button -->
                <div>

                 <a :href="route('workshops.index')"
                    class="text-indigo-600 hover:underline"
                    >
                    ← Back to Workshops
                  </a>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
