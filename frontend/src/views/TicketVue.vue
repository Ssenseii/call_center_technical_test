<template>
	<div class="container min-w-full p-6 min-h-screen bg-gray-900 text-gray-100">
		<h1 class="text-2xl font-bold mb-6 text-blue-400">Ticket Management</h1>

		<div class="md:flex md:space-x-6">
			<!-- Tickets List -->
			<div
				class="md:w-1/2 bg-gray-800 shadow-lg rounded-lg p-4 mb-6 md:mb-0 border border-gray-700"
			>
				<h2 class="text-xl font-semibold mb-3 text-blue-300">All Tickets</h2>
				<template v-if="ticketStore.loading">
					<p class="text-gray-400">Loading…</p>
				</template>
				<template v-else>
					<ul class="divide-y divide-gray-700">
						<li
							v-for="t in ticketStore.tickets"
							:key="t.id"
							class="flex justify-between items-center py-3 hover:bg-gray-700 px-2 rounded transition-colors"
						>
							<button
								@click="selectTicket(t.id)"
								class="text-left flex-1 hover:text-blue-300 transition-colors"
							>
								<span class="font-medium text-blue-400">#{{ t.id }}</span> –
								{{ t.title }}
							</button>
							<div class="space-x-2">
								<button
									@click="startEdit(t)"
									class="px-3 py-1 text-sm bg-yellow-600 hover:bg-yellow-700 rounded-md"
								>
									Edit
								</button>
								<button
									@click="remove(t.id)"
									class="px-3 py-1 text-sm bg-red-600 hover:bg-red-700 rounded-md text-white"
								>
									Delete
								</button>
							</div>
						</li>
					</ul>
				</template>
				<p v-if="ticketStore.hasError" class="text-red-400 mt-2 bg-red-900/50 p-2 rounded">
					Error: {{ ticketStore.error }}
				</p>
			</div>

			<!-- Form & Details -->
			<div class="md:w-1/2 space-y-6">
				<!-- Create / Edit Form -->
				<div class="bg-gray-800 shadow-lg rounded-lg p-4 border border-gray-700">
					<h2 class="text-xl font-semibold mb-3 text-blue-300">
						{{ isEditing ? "Edit Ticket" : "New Ticket" }}
					</h2>
					<form @submit.prevent="submitForm">
						<div class="mb-4">
							<label class="block text-sm font-medium mb-1">Title</label>
							<input
								v-model="form.title"
								type="text"
								required
								class="w-full border border-gray-600 bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500"
							/>
						</div>
						<div class="mb-4">
							<label class="block text-sm font-medium mb-1">Description</label>
							<textarea
								v-model="form.description"
								required
								class="w-full border border-gray-600 bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500"
							></textarea>
						</div>
						<div class="mb-4">
							<label class="block text-sm font-medium mb-1">Status</label>
							<select
								v-model="form.status"
								class="w-full border border-gray-600 bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500"
							>
								<option value="open">Open</option>
								<option value="in_progress">In Progress</option>
								<option value="resolved">Resolved</option>
							</select>
						</div>
						<div class="mb-4">
							<label class="block text-sm font-medium mb-1"
								>Attach to Call (optional)</label
							>
							<select
								v-model="form.call_id"
								@change="logCallId"
								class="w-full border border-gray-600 bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500"
							>
								<option :value="null">— none —</option>
								<option
									v-for="c in ticketStore.availableCalls"
									:key="c.id"
									:value="c.id"
								>
									#{{ c.id }} – {{ c.subject }}
								</option>
							</select>
						</div>
						<div class="flex space-x-3">
							<button
								type="submit"
								class="flex-1 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
							>
								{{ isEditing ? "Update" : "Create" }}
							</button>
							<button
								v-if="isEditing"
								type="button"
								@click="cancelEdit"
								class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white rounded"
							>
								Cancel
							</button>
						</div>
					</form>
					<p
						v-if="ticketStore.hasError"
						class="text-red-400 mt-2 bg-red-900/50 p-2 rounded"
					>
						Error: {{ ticketStore.error }}
					</p>
				</div>

				<!-- Ticket Details + Comments -->
				<div
					v-if="ticketStore.currentTicket"
					class="bg-gray-800 shadow-lg rounded-lg p-4 border border-gray-700"
				>
					<h2 class="text-xl font-semibold mb-3 text-blue-300">Ticket Details</h2>
					<div class="space-y-2 mb-4">
						<div>
							<span class="font-medium text-gray-300">ID:</span>
							{{ ticketStore.currentTicket.id }}
						</div>
						<div>
							<span class="font-medium text-gray-300">Title:</span>
							{{ ticketStore.currentTicket.title }}
						</div>
						<div>
							<span class="font-medium text-gray-300">Description:</span>
							{{ ticketStore.currentTicket.description }}
						</div>
						<div>
							<span class="font-medium text-gray-300">Status:</span>
							{{ ticketStore.currentTicket.status }}
						</div>
						<div v-if="ticketStore.currentTicket.call">
							<span class="font-medium text-gray-300">Call #:</span>
							{{ ticketStore.currentTicket.call.id }} –
							{{ ticketStore.currentTicket.call.subject }}
						</div>
						<div>
							<span class="font-medium text-gray-300">Created At:</span>
							{{ formatDate(ticketStore.currentTicket.created_at) }}
						</div>
					</div>

					<div class="mb-4">
						<label class="block text-sm font-medium mb-1">Change Status</label>
						<div class="flex items-center">
							<select
								v-model="statusToChange"
								class="border border-gray-600 bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring focus:ring-green-500"
							>
								<option value="open">Open</option>
								<option value="in_progress">In Progress</option>
								<option value="resolved">Resolved</option>
							</select>
							<button
								@click="updateStatus()"
								class="ml-2 px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700"
							>
								Update
							</button>
						</div>
					</div>

					<!-- Comment -->
					<div>
						<h3 class="text-lg font-semibold mb-2 text-blue-300">Comments</h3>
						<!-- Add Comment Form -->
						<div class="mb-4">
							<textarea
								v-model="newComment"
								placeholder="Add a comment..."
								class="w-full border border-gray-600 bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500 mb-2"
							></textarea>
							<button
								@click="addComment"
								class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700"
								:disabled="commentStore.loading"
							>
								{{ commentStore.loading ? "Posting..." : "Post Comment" }}
							</button>
							<p v-if="commentStore.error" class="text-red-400 mt-2 text-sm">
								{{ commentStore.error }}
							</p>
						</div>

						<ul class="space-y-2">
							<li
								v-for="comment in ticketStore.currentTicket.comments"
								:key="comment.id"
								class="border border-gray-600 p-2 rounded bg-gray-700"
							>
								<div class="flex justify-between items-start">
									<div>
										<div class="text-sm text-gray-300">
											{{ comment.user.name }} said:
										</div>
										<div class="text-white">{{ comment.content }}</div>
										<div class="text-xs text-gray-400">
											{{ formatDate(comment.created_at) }}
										</div>
									</div>
									<div
										v-if="auth.user?.id === comment.user_id"
										class="flex space-x-2"
									>
										<button
											@click="startEditComment(comment)"
											class="text-xs px-2 py-1 bg-yellow-600 hover:bg-yellow-700 rounded"
										>
											Edit
										</button>
										<button
											@click="deleteComment(comment.id)"
											class="text-xs px-2 py-1 bg-red-600 hover:bg-red-700 rounded"
										>
											Delete
										</button>
									</div>
								</div>
								<!-- Edit Comment Form -->
								<div v-if="editingCommentId === comment.id" class="mt-2">
									<textarea
										v-model="editedCommentContent"
										class="w-full border border-gray-600 bg-gray-700 text-white rounded px-3 py-2 focus:outline-none focus:ring focus:ring-blue-500 mb-2"
									></textarea>
									<div class="flex space-x-2">
										<button
											@click="updateComment(comment.id)"
											class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm"
										>
											Save
										</button>
										<button
											@click="cancelEditComment"
											class="px-2 py-1 bg-gray-600 text-white rounded hover:bg-gray-500 text-sm"
										>
											Cancel
										</button>
									</div>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useTicketStore } from "@/stores/ticket";
import { useCommentStore } from "@/stores/comment";
import { useAuthStore } from "@/stores/auth";

const ticketStore = useTicketStore();
const auth = useAuthStore();
const commentStore = useCommentStore();

const form = ref({
	title: "",
	description: "",
	status: "open",
	call_id: null,
});
const isEditing = ref(false);
const statusToChange = ref("open");
const newComment = ref("");
const editingCommentId = ref(null);
const editedCommentContent = ref("");

onMounted(() => {
	ticketStore.fetchTickets();
	ticketStore.fetchAvailableCalls();
});

function selectTicket(id) {
	ticketStore.fetchTicket(id);
	isEditing.value = false;
}

function startEdit(ticket) {
	form.value = {
		id: ticket.id,
		title: ticket.title,
		description: ticket.description,
		status: ticket.status,
		call_id: ticket.call?.id || null, 
	};
	statusToChange.value = ticket.status;
	isEditing.value = true;
	ticketStore.currentTicket = ticket;
}

function cancelEdit() {
	isEditing.value = false;
	resetForm();
}

function resetForm() {
	form.value = {
		title: "",
		description: "",
		status: "open",
		call_id: null,
	};
}

async function submitForm() {
	try {
		if (isEditing.value) {
			await ticketStore.updateTicket(form.value);
		} else {
			await ticketStore.createTicket(form.value);
		}
		resetForm();
		isEditing.value = false;
	} catch (error) {
		console.error("Form submission error:", error);
	}
}

async function remove(id) {
	if (confirm("Delete this ticket?")) {
		await ticketStore.deleteTicket(id);
	}
}

async function updateStatus() {
	if (!ticketStore.currentTicket) return;
	await ticketStore.updateStatus({
		id: ticketStore.currentTicket.id,
		status: statusToChange.value,
	});
}

function formatDate(dateString) {
	return new Date(dateString).toLocaleString();
}

/* Comment Functions */
const addComment = async () => {
	if (!newComment.value.trim() || !ticketStore.currentTicket) return;

	try {
		const comment = await commentStore.createComment(
			ticketStore.currentTicket.id,
			newComment.value
		);

		if (!ticketStore.currentTicket.comments) {
			ticketStore.currentTicket.comments = [];
		}

		ticketStore.currentTicket.comments.unshift({
			...comment,
			user: comment.user || { name: "Current User" },
		});

		newComment.value = "";
	} catch (error) {
		console.error("Failed to add comment:", error);
	}
};

const startEditComment = (comment) => {
	editingCommentId.value = comment.id;
	editedCommentContent.value = comment.content;
};

const cancelEditComment = () => {
	editingCommentId.value = null;
	editedCommentContent.value = "";
};

const updateComment = async (commentId) => {
	if (!editedCommentContent.value.trim()) return;

	try {
		const updatedComment = await commentStore.updateComment(
			commentId,
			editedCommentContent.value
		);

		const commentIndex = ticketStore.currentTicket.comments.findIndex(
			(c) => c.id === commentId
		);
		if (commentIndex !== -1) {
			ticketStore.currentTicket.comments[commentIndex] = {
				...ticketStore.currentTicket.comments[commentIndex],
				...updatedComment,
			};
		}

		cancelEditComment();
	} catch (error) {
		console.error("Failed to update comment:", error);
	}
};

const deleteComment = async (commentId) => {
	if (!confirm("Are you sure you want to delete this comment?")) return;

	try {
		await commentStore.deleteComment(commentId);
		ticketStore.currentTicket.comments = ticketStore.currentTicket.comments.filter(
			(c) => c.id !== commentId
		);
	} catch (error) {
		console.error("Failed to delete comment:", error);
	}
};
</script>
