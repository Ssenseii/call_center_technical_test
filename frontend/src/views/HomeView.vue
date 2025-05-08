<script setup>
import { useRouter } from "vue-router";

const router = useRouter();

const navigateToTickets = () => {
	router.push("/tickets");
};

const navigateToCalls = () => {
	router.push("/calls");
};
</script>

<template>
	<main class="min-h-screen bg-gray-900 text-gray-100 py-12 px-6">
		<div class="max-w-md mx-auto text-center">
			<!-- Greeting -->
			<h1 class="text-4xl font-bold mb-6">Hello!</h1>
			<p class="text-gray-400 mb-10 text-lg">Welcome to our call center support portal</p>

			<!-- Action Buttons -->
			<div class="flex flex-col space-y-4">
				<button
					class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200"
					@click="navigateToTickets"
				>
					View Tickets
				</button>

				<button
					class="bg-gray-800 hover:bg-gray-700 border border-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200"
					@click="navigateToCalls"
				>
					View Calls
				</button>
			</div>
		</div>
	</main>
</template>
