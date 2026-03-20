<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    workshop: Object,
});

const form = useForm({
    title: props.workshop.title,
    description: props.workshop.description,
    start_date: props.workshop.starts_at.slice(0, 10),
    start_time: props.workshop.starts_at.slice(11, 16),
    end_date: props.workshop.ends_at.slice(0, 10),
    end_time: props.workshop.ends_at.slice(11, 16),
    starts_at: props.workshop.starts_at,
    ends_at: props.workshop.ends_at,
    capacity: props.workshop.capacity,
});

function submit() {
    form.starts_at = `${form.start_date} ${form.start_time}:00`;
    form.ends_at = `${form.end_date} ${form.end_time}:00`;
    form.put(route('workshops.update', props.workshop.id));
}
</script>

<template>
    <Head title="Edit Workshop" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Workshop
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                    <form @submit.prevent="submit" class="space-y-6">

                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <p v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</p>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <p v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</p>
                        </div>

                        <!-- Starts At -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input
                                    v-model="form.start_date"
                                    type="date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Time</label>
                                <input
                                    v-model="form.start_time"
                                    type="time"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <p v-if="form.errors.starts_at" class="text-red-500 text-sm mt-1 col-span-2">{{ form.errors.starts_at }}</p>
                        </div>

                        <!-- Ends At -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <input
                                    v-model="form.end_date"
                                    type="date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Time</label>
                                <input
                                    v-model="form.end_time"
                                    type="time"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <p v-if="form.errors.ends_at" class="text-red-500 text-sm mt-1 col-span-2">{{ form.errors.ends_at }}</p>
                        </div>

                        <!-- Capacity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Capacity</label>
                            <input
                                v-model="form.capacity"
                                type="number"
                                min="1"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                            <p v-if="form.errors.capacity" class="text-red-500 text-sm mt-1">{{ form.errors.capacity }}</p>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end gap-3">

                           <a :href="route('workshops.index')"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                            >
                            Cancel
                            </a>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50"
                            >
                                Save Changes
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
