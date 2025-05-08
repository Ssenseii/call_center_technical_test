<template>
	<div class="container min-w-full p-6 min-h-screen bg-gray-900 text-gray-100">
		<!-- Title -->
		<h1 class="text-2xl font-bold mb-6 text-blue-400">Call Management</h1>

		<div class="md:flex md:space-x-6">
			<!-- Calls List -->
			<div
				class="md:w-1/2 bg-gray-800 shadow-lg rounded-lg p-4 mb-6 md:mb-0 border border-gray-700"
			>
				<h2 class="text-xl font-semibold mb-3 text-blue-300">All Calls</h2>
				<template v-if="callStore.loading">
					<div class="flex justify-center items-center py-8">
						<div
							class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"
						></div>
					</div>
				</template>
				<template v-else>
					<ul class="divide-y divide-gray-700">
						<li
							v-for="call in callStore.calls"
							:key="call.id"
							class="flex justify-between items-center py-3 hover:bg-gray-700 px-2 rounded transition-colors"
						>
							<button
								@click="selectCall(call.id)"
								class="text-left flex-1 hover:text-blue-300 transition-colors"
							>
								<span class="font-medium text-blue-400">#{{ call.id }}</span> -
								{{ call.subject }}
							</button>
							<div class="space-x-2">
								<button
									@click="startEdit(call)"
									class="px-3 py-1 text-sm bg-yellow-600 hover:bg-yellow-700 rounded-md transition-colors"
								>
									Edit
								</button>
								<button
									@click="removeCall(call.id)"
									class="px-3 py-1 text-sm bg-red-600 hover:bg-red-700 rounded-md transition-colors"
								>
									Delete
								</button>
							</div>
						</li>
					</ul>
				</template>
				<p v-if="callStore.hasError" class="text-red-400 mt-2 bg-red-900/50 p-2 rounded">
					Error: {{ callStore.error }}
				</p>
			</div>

			<!-- Form & Details -->
			<div class="md:w-1/2 space-y-6">
				<!-- Create / Edit Form -->
				<div class="bg-gray-800 shadow-lg rounded-lg p-4 border border-gray-700">
					<h2 class="text-xl font-semibold mb-3 text-blue-300">
						{{ isEditing ? "Edit Call" : "New Call" }}
					</h2>
					<form @submit.prevent="submitForm">
						<div class="mb-4">
							<label class="block text-sm font-medium mb-1 text-gray-300"
								>Subject</label
							>
							<input
								v-model="callForm.subject"
								type="text"
								required
								class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-100"
							/>
						</div>
						<div class="mb-4">
							<label class="block text-sm font-medium mb-1 text-gray-300">Time</label>
							<input
								v-model="callForm.time"
								type="time"
								required
								class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-100"
							/>
						</div>
						<div class="mb-4">
							<label class="block text-sm font-medium mb-1 text-gray-300"
								>Duration (minutes)</label
							>
							<input
								v-model.number="callForm.duration"
								type="number"
								min="1"
								required
								class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-100"
							/>
						</div>
						<div class="flex space-x-3">
							<button
								type="submit"
								class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors"
							>
								{{ isEditing ? "Update" : "Create" }}
							</button>
							<button
								v-if="isEditing"
								type="button"
								@click="cancelEdit"
								class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-500 rounded-md transition-colors"
							>
								Cancel
							</button>
						</div>
					</form>
					<p
						v-if="callStore.hasError"
						class="text-red-400 mt-2 bg-red-900/50 p-2 rounded"
					>
						Error: {{ callStore.error }}
					</p>
				</div>

				<!-- Show Details -->
				<div
					v-if="callStore.currentCall"
					class="bg-gray-800 shadow-lg rounded-lg p-4 border border-gray-700"
				>
					<h2 class="text-xl font-semibold mb-3 text-blue-300">Call Details</h2>
					<div class="space-y-2 text-gray-300">
						<div>
							<span class="font-medium text-gray-200">ID:</span>
							{{ callStore.currentCall.id }}
						</div>
						<div>
							<span class="font-medium text-gray-200">Subject:</span>
							{{ callStore.currentCall.subject }}
						</div>
						<div>
							<span class="font-medium text-gray-200">Time:</span>
							{{ callStore.currentCall.time }}
						</div>
						<div>
							<span class="font-medium text-gray-200">Duration:</span>
							{{ callStore.currentCall.duration }} minutes
						</div>
						<div>
							<span class="font-medium text-gray-200">Created At:</span>
							{{ formatDate(callStore.currentCall.created_at) }}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useCallStore } from "@/stores/call";
import { useAuthStore } from "@/stores/auth";

const callStore = useCallStore();
const authStore = useAuthStore();

const callForm = ref({ subject: "", time: "", duration: 1 });
const isEditing = ref(false);

onMounted(() => {
	callStore.fetchCalls();
});

function selectCall(id) {
	callStore.fetchCall(id);
	isEditing.value = false;
}

function startEdit(call) {
	callForm.value = {
		subject: call.subject,
		time: call.time,
		duration: call.duration,
		id: call.id,
	};
	isEditing.value = true;
	callStore.currentCall = call;
}

function cancelEdit() {
	isEditing.value = false;
	callForm.value = { subject: "", time: "", duration: 1 };
}

async function submitForm() {
	try {
		if (isEditing.value) {
			await callStore.updateCall({
				id: callForm.value.id,
				subject: callForm.value.subject,
				time: callForm.value.time,
				duration: callForm.value.duration,
			});
		} else {
			await callStore.createCall({
				subject: callForm.value.subject,
				time: callForm.value.time,
				duration: callForm.value.duration,
			});
		}
		callForm.value = { subject: "", time: "", duration: 1 };
		isEditing.value = false;
	} catch (err) {
		// error handled in store
	}
}

async function removeCall(id) {
	if (confirm("Are you sure you want to delete this call?")) {
		await callStore.deleteCall(id);
	}
}

async function checkTicket(id) {
	await callStore.checkForTicket(id);
}

function formatDate(dateStr) {
	return new Date(dateStr).toLocaleString();
}
</script>

<style scoped>
/* Add any additional scoped styles here */
</style>
