<template>
    <Head title="Staff Management" />
    <AuthenticatedLayout>
        <div class="flex h-screen bg-gray-100">
            <!-- Sidebar -->
            <aside class="w-64 bg-white shadow-md">
                <div class="p-6 flex flex-col items-center">
                    <img
                        :src="`https://ui-avatars.com/api/?name=${$page.props.auth.user.name}&background=random`"
                        alt="User Avatar"
                        class="w-24 h-24 rounded-full mb-4 border-4 border-blue-500 shadow-lg"
                    />
                    <h2 class="text-xl font-bold text-gray-800">
                        {{ $page.props.auth.user.name }}
                    </h2>
                    <p class="text-sm text-gray-600 mb-2">
                        {{ $page.props.auth.user.email }}
                    </p>
                    <p
                        class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold"
                    >
                        {{ getRole($page.props.auth.user.role_id) }}
                    </p>
                </div>
                <nav class="mt-6">
                    <a
                        v-for="item in navItems"
                        :key="item.name"
                        :href="item.href"
                        class="flex items-center px-6 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors duration-200"
                    >
                        <component :is="item.icon" class="h-5 w-5 mr-3" />
                        {{ item.name }}
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                <div class="container mx-auto px-6 py-8">
                    <h1 class="text-4xl font-bold text-gray-800 mb-8">
                        Staff Dashboard
                    </h1>

                    <!-- Dashboard Stats -->
                    <section
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12"
                    >
                        <div
                            v-for="stat in dashboardStats"
                            :key="stat.title"
                            class="bg-white p-6 rounded-xl shadow-lg transform hover:scale-105 transition-all duration-300"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-semibold text-gray-700">
                                    {{ stat.title }}
                                </h3>
                                <component
                                    :is="stat.icon"
                                    class="h-8 w-8 text-gray-500"
                                />
                            </div>
                            <p class="text-3xl font-bold text-gray-800">
                                {{ stat.value }}
                            </p>
                        </div>
                    </section>

                    <!-- Tribe Management -->
                    <section id="tribes">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-3xl font-bold text-gray-800">
                                Philippine Tribe
                            </h2>
                            <button
                                @click="openTribeModal()"
                                class="bg-blue-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-blue-700 transition-colors duration-300 flex items-center shadow-lg"
                            >
                                <PlusIcon class="h-5 w-5 mr-2" />
                                Add New Tribe
                            </button>
                        </div>

                        <!-- Tribe Grid -->
                        <div
                            v-if="tribes.length > 0"
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                        >
                            <div
                                v-for="tribe in tribes"
                                :key="tribe.id"
                                class="bg-white overflow-hidden shadow-lg rounded-xl transition-all duration-300 transform hover:scale-105"
                            >
                                <img
                                    :src="tribe.url_image || '/placeholder.svg'"
                                    :alt="tribe.tribe_name"
                                    class="w-full h-48 object-cover"
                                />
                                <div class="p-6">
                                    <h3
                                        class="text-xl font-semibold text-gray-900 mb-2"
                                    >
                                        {{ tribe.tribe_name }}
                                    </h3>
                                    <p
                                        :style="{
                                            color: getCategoryColor(
                                                tribe.category_id
                                            ).text,
                                            backgroundColor: getCategoryColor(
                                                tribe.category_id
                                            ).bg,
                                        }"
                                        class="text-sm mb-4 font-medium inline-block px-2 py-1 rounded-full"
                                    >
                                        {{ getCategoryName(tribe.category_id) }}
                                    </p>
                                    <div
                                        class="flex items-center justify-between text-gray-500 mb-4"
                                    >
                                        <span class="flex items-center">
                                            <UsersIcon class="w-4 h-4 mr-1" />
                                            {{ tribe.population }}
                                        </span>
                                        <span class="flex items-center">
                                            <MapPinIcon class="w-4 h-4 mr-1" />
                                            {{ tribe.region }} region
                                        </span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <button
                                            @click="viewTribeDetails(tribe)"
                                            class="bg-blue-600 text-white px-4 py-2 rounded-md transition duration-300 ease-in-out hover:bg-blue-700"
                                        >
                                            View more details
                                        </button>
                                        <div class="flex space-x-2">
                                            <button
                                                @click="openTribeModal(tribe)"
                                                class="bg-blue-500 text-white p-2 rounded-full hover:bg-blue-600 transition-colors duration-300"
                                            >
                                                <PencilIcon class="h-5 w-5" />
                                            </button>
                                            <button
                                                @click="
                                                    confirmDeleteTribe(tribe.id)
                                                "
                                                class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors duration-300"
                                            >
                                                <TrashIcon class="h-5 w-5" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-12">
                            <FlagIcon
                                class="h-24 w-24 text-gray-400 mx-auto mb-4"
                            />
                            <p class="text-gray-500 text-lg">
                                No tribes found. Add a new tribes to get
                                started!
                            </p>
                        </div>
                    </section>
                </div>
            </main>

            <!-- Add/Edit Tribe Modal -->
            <TransitionRoot appear :show="showTribeModal" as="template">
                <Dialog as="div" @close="closeTribeModal" class="relative z-50">
                    <div class="fixed inset-0 overflow-y-auto">
                        <div
                            class="flex min-h-full items-center justify-center p-4 text-center"
                        >
                            <TransitionChild
                                as="template"
                                enter="duration-300 ease-out"
                                enter-from="opacity-0 scale-95"
                                enter-to="opacity-100 scale-100"
                                leave="duration-200 ease-in"
                                leave-from="opacity-100 scale-100"
                                leave-to="opacity-0 scale-95"
                            >
                                <DialogPanel
                                    class="w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                                >
                                    <DialogTitle
                                        as="h3"
                                        class="text-lg font-medium leading-6 text-gray-900 mb-4"
                                    >
                                        {{
                                            editingTribe
                                                ? "Edit Tribe"
                                                : "Create New Tribe"
                                        }}
                                    </DialogTitle>
                                    <form
                                        @submit.prevent="confirmSubmitTribe"
                                        class="space-y-4"
                                    >
                                        <div>
                                            <label
                                                for="tribe_name"
                                                class="block text-sm font-medium text-gray-700"
                                                >Tribe Name</label
                                            >
                                            <input
                                                type="text"
                                                id="tribe_name"
                                                v-model="tribeForm.tribe_name"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                placeholder="Enter tribe name"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                for="description"
                                                class="block text-sm font-medium text-gray-700"
                                                >Background</label
                                            >
                                            <textarea
                                                id="description"
                                                v-model="tribeForm.description"
                                                required
                                                rows="3"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                placeholder="Enter a detailed description of the tribe"
                                            ></textarea>
                                        </div>
                                        <div>
                                            <label
                                                for="language"
                                                class="block text-sm font-medium text-gray-700"
                                                >Language</label
                                            >
                                            <textarea
                                                id="language"
                                                v-model="tribeForm.language"
                                                required
                                                rows="4"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                placeholder="Tribe Language (e.g., Tausug)"
                                            ></textarea>
                                        </div>
                                        <div>
                                            <label
                                                for="cultural_practices"
                                                class="block text-sm font-medium text-gray-700"
                                                >Cultural Practices</label
                                            >
                                            <textarea
                                                id="cultural_practices"
                                                v-model="
                                                    tribeForm.cultural_practices
                                                "
                                                required
                                                rows="4"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                placeholder="Enter the Tribe's Cultural Practices"
                                            ></textarea>
                                        </div>
                                        <div>
                                            <label
                                                for="population"
                                                class="block text-sm font-medium text-gray-700"
                                                >Population</label
                                            >
                                            <input
                                                type="text"
                                                id="population"
                                                v-model="tribeForm.population"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                placeholder="Population (Estimated)"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                for="region"
                                                class="block text-sm font-medium text-gray-700"
                                                >Region</label
                                            >
                                            <input
                                                type="text"
                                                id="region"
                                                v-model="tribeForm.region"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                                placeholder="e.g. Caraga"
                                            />
                                        </div>
                                        <div>
                                            <label
                                                for="category"
                                                class="block text-sm font-medium text-gray-700"
                                                >Category</label
                                            >
                                            <select
                                                id="category"
                                                v-model="tribeForm.category_id"
                                                required
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            >
                                                <option value="" disabled>
                                                    Select a category
                                                </option>
                                                <option
                                                    v-for="category in categories"
                                                    :key="category.id"
                                                    :value="category.id"
                                                >
                                                    {{ category.category_name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div
                                            class="mt-6 flex justify-end space-x-3"
                                        >
                                            <button
                                                type="button"
                                                @click="closeTribeModal"
                                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                            >
                                                Cancel
                                            </button>
                                            <button
                                                type="submit"
                                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            >
                                                {{
                                                    editingTribe
                                                        ? "Edit Tribe"
                                                        : "Add Tribe"
                                                }}
                                            </button>
                                        </div>
                                    </form>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>

            <!-- Tribe Details Modal -->
            <TransitionRoot appear :show="!!selectedTribe" as="template">
                <Dialog
                    as="div"
                    @close="closeTribeDetails"
                    class="relative z-50"
                >
                    <div class="fixed inset-0 overflow-y-auto">
                        <div
                            class="flex min-h-full items-center justify-center p-4 text-center"
                        >
                            <TransitionChild
                                as="template"
                                enter="duration-300 ease-out"
                                enter-from="opacity-0 scale-95"
                                enter-to="opacity-100 scale-100"
                                leave="duration-200 ease-in"
                                leave-from="opacity-100 scale-100"
                                leave-to="opacity-0 scale-95"
                            >
                                <DialogPanel
                                    class="w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                                >
                                    <div
                                        class="flex justify-between items-center mb-4"
                                    >
                                        <DialogTitle
                                            as="h2"
                                            class="text-3xl font-bold text-gray-800"
                                        >
                                            {{ selectedTribe?.tribe_name }}
                                        </DialogTitle>
                                        <button
                                            @click="closeTribeDetails"
                                            class="text-gray-500 hover:text-gray-700"
                                        >
                                            <XIcon class="h-6 w-6" />
                                        </button>
                                    </div>
                                    <img
                                        :src="
                                            selectedTribe?.url_image ||
                                            '/placeholder.svg'
                                        "
                                        :alt="selectedTribe?.tribe_name"
                                        class="w-full h-64 object-cover rounded-xl mb-4"
                                    />
                                    <div
                                        class="flex justify-between items-center text-gray-600 mb-4"
                                    >
                                        <span class="flex items-center">
                                            <UsersIcon class="w-5 h-5 mr-2" />
                                            {{ selectedTribe?.population }}
                                        </span>
                                        <span class="flex items-center">
                                            <MapPinIcon class="w-5 h-5 mr-2" />
                                            {{ selectedTribe?.region }} region
                                        </span>
                                        <span
                                            :style="{
                                                color: getCategoryColor(
                                                    selectedTribe?.category_id
                                                ).text,
                                                backgroundColor:
                                                    getCategoryColor(
                                                        selectedTribe?.category_id
                                                    ).bg,
                                            }"
                                            class="px-3 py-1 rounded-full text-sm font-medium"
                                        >
                                            {{
                                                getCategoryName(
                                                    selectedTribe?.category_id
                                                )
                                            }}
                                        </span>
                                    </div>
                                    <div
                                        class="space-y-4 max-h-[calc(100vh-24rem)] overflow-y-auto pr-4 custom-scrollbar"
                                    >
                                        <div>
                                            <h3
                                                class="text-xl font-semibold mb-2"
                                            >
                                                Description
                                            </h3>
                                            <p class="text-gray-700">
                                                {{ selectedTribe?.description }}
                                            </p>
                                        </div>
                                        <div>
                                            <h3
                                                class="text-xl font-semibold mb-2"
                                            >
                                                Language
                                            </h3>
                                            <ul
                                                class="list-disc list-inside text-gray-700"
                                            >
                                                <li
                                                    v-for="language in selectedTribe?.language.split(
                                                        '\n'
                                                    )"
                                                    :key="language"
                                                    class="mb-2 break-words"
                                                >
                                                    {{ language.trim() }}
                                                </li>
                                            </ul>
                                        </div>
                                        <div>
                                            <h3
                                                class="text-xl font-semibold mb-2"
                                            >
                                                Cultural Practices
                                            </h3>
                                            <ol
                                                class="list-decimal list-inside text-gray-700"
                                            >
                                                <li
                                                    v-for="(
                                                        step, index
                                                    ) in selectedTribe?.cultural_practices.split(
                                                        '\n'
                                                    )"
                                                    :key="index"
                                                    class="mb-2 break-words"
                                                >
                                                    {{ step.trim() }}
                                                </li>
                                            </ol>
                                        </div>
                                    </div>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>

            <!-- Confirmation Modal -->
            <TransitionRoot appear :show="showConfirmModal" as="template">
                <Dialog
                    as="div"
                    @close="closeConfirmModal"
                    class="relative z-50"
                >
                    <div class="fixed inset-0 overflow-y-auto">
                        <div
                            class="flex min-h-full items-center justify-center p-4 text-center"
                        >
                            <TransitionChild
                                as="template"
                                enter="duration-300 ease-out"
                                enter-from="opacity-0 scale-95"
                                enter-to="opacity-100 scale-100"
                                leave="duration-200 ease-in"
                                leave-from="opacity-100 scale-100"
                                leave-to="opacity-0 scale-95"
                            >
                                <DialogPanel
                                    class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                                >
                                    <DialogTitle
                                        as="h3"
                                        class="text-lg font-medium leading-6 text-gray-900 mb-4"
                                    >
                                        {{ confirmModalConfig.title }}
                                    </DialogTitle>
                                    <p class="text-sm text-gray-500 mb-4">
                                        {{ confirmModalConfig.message }}
                                    </p>
                                    <div
                                        class="mt-4 flex justify-end space-x-3"
                                    >
                                        <button
                                            @click="closeConfirmModal"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            @click="confirmAction"
                                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            Confirm
                                        </button>
                                    </div>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>

            <!-- Creative Alert -->
            <TransitionRoot appear :show="showCreativeAlert" as="template">
                <Dialog
                    as="div"
                    @close="closeCreativeAlert"
                    class="relative z-50"
                >
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0"
                        enter-to="opacity-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100"
                        leave-to="opacity-0"
                    >
                        <div class="fixed inset-0 bg-black bg-opacity-25" />
                    </TransitionChild>

                    <div class="fixed inset-0 overflow-y-auto">
                        <div
                            class="flex min-h-full items-center justify-center p-4 text-center"
                        >
                            <TransitionChild
                                as="template"
                                enter="duration-300 ease-out"
                                enter-from="opacity-0 scale-95"
                                enter-to="opacity-100 scale-100"
                                leave="duration-200 ease-in"
                                leave-from="opacity-100 scale-100"
                                leave-to="opacity-0 scale-95"
                            >
                                <DialogPanel
                                    class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
                                >
                                    <div
                                        class="flex items-center justify-center mb-4"
                                    >
                                        <div
                                            class="bg-green-100 rounded-full p-3"
                                        >
                                            <CheckIcon
                                                class="h-6 w-6 text-green-600"
                                            />
                                        </div>
                                    </div>
                                    <DialogTitle
                                        as="h3"
                                        class="text-lg font-medium leading-6 text-gray-900 text-center mb-2"
                                    >
                                        Tribe
                                        {{ creativeAlertConfig.action }}
                                        Successfully!
                                    </DialogTitle>
                                    <p
                                        class="text-sm text-gray-500 text-center mb-4"
                                    >
                                        {{ creativeAlertConfig.message }}
                                    </p>
                                    <div class="mt-4 flex justify-center">
                                        <button
                                            type="button"
                                            class="inline-flex justify-center rounded-md border border-transparent bg-blue-100 px-4 py-2 text-sm font-medium text-blue-900 hover:bg-blue-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                            @click="closeCreativeAlert"
                                        >
                                            Got it, thanks!
                                        </button>
                                    </div>
                                </DialogPanel>
                            </TransitionChild>
                        </div>
                    </div>
                </Dialog>
            </TransitionRoot>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Head, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    TransitionChild,
    TransitionRoot,
} from "@headlessui/vue";
import {
    ChartBarIcon,
    ClockIcon,
    UserGroupIcon,
    PlusIcon,
    PencilIcon,
    TrashIcon,
    XMarkIcon as XIcon,
    HomeIcon,
    BookOpenIcon,
    UsersIcon,
    BookmarkIcon,
    FireIcon,
    TagIcon,
    CheckIcon,
    FlagIcon,
    MapPinIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    staffs: Array,
    roles: Array,
    users: Array,
    stats: {
        type: Array,
        default: () => ({
            total_luzon_tribes: 0,
            total_visayan_tribes: 0,
            total_mindanao_tribes: 0,
            total_moro_tribes: 0,
        }),
    },
    tribes: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    activityLogs: {
        type: Array,
        default: () => [],
    },
});

// Reactive data
const tribes = ref(props.tribes);
const categories = ref(props.categories);
const roles = ref(props.roles || []);
const totalTribes = ref(0);
const totalTribesCreated = ref(0);
const totalCategories = ref(0);
const recentTribes = ref([]);

const showTribeModal = ref(false);
const showConfirmModal = ref(false);
const showCreativeAlert = ref(false);
const editingTribe = ref(null);
const selectedTribe = ref(null);

const tribeForm = ref({
    tribe_name: "",
    description: "",
    language: "",
    cultural_practices: "",
    category_id: "",
    population: "",
    region: "",
    url_image: null,
});

const confirmModalConfig = ref({
    title: "",
    message: "",
    onConfirm: null,
});

const creativeAlertConfig = ref({
    action: "",
    message: "",
});

// Navigation items
const navItems = [
    { name: "Dashboard", href: "#dashboard", icon: HomeIcon },
    { name: "Tribes", href: "#tribes", icon: FlagIcon },
];

// Computed properties
const dashboardStats = computed(() => [
    {
        title: "Total Luzon Tribes",
        value: props.stats[0].total_luzon_tribes,
        icon: FlagIcon,
    },
    {
        title: "Total Visayan Tribes",
        value: props.stats[0].total_visayan_tribes,
        icon: FlagIcon,
    },
    {
        title: "Total Mindanao Tribes",
        value: props.stats[0].total_mindanao_tribes,
        icon: FlagIcon,
    },
    {
        title: "Total Moro Tribes",
        value: props.stats[0].total_moro_tribes,
        icon: FlagIcon,
    },
]);

// Methods
const getRole = (roleId) => {
    const role = roles.value.find((r) => r.id === roleId);
    return role ? role.role_user : "Unknown";
};

const getCategoryName = (categoryId) => {
    const category = categories.value.find((c) => c.id === categoryId);
    return category ? category.category_name : "Uncategorized";
};

const getCategoryColor = (categoryId) => {
    const category = categories.value.find((c) => c.id === categoryId);
    if (category) {
        switch (category.category_name.toLowerCase()) {
            case "main course":
                return { text: "#FFA500", bg: "#FFF3E0" };
            case "appetizer":
                return { text: "#FFD700", bg: "#FFFDE7" };
            case "dessert":
                return { text: "#FF69B4", bg: "#FCE4EC" };
            case "beverage":
                return { text: "#0000FF", bg: "#E8F3FF" }; // Changed to blue
            case "side dish":
                return { text: "#32CD32", bg: "#F1F8E9" };
            default:
                return { text: "#000000", bg: "#FFFFFF" };
        }
    }
    return { text: "#000000", bg: "#FFFFFF" };
};

const openTribeModal = (tribe = null) => {
    editingTribe.value = tribe;
    if (tribe) {
        tribeForm.value = { ...tribe };
    } else {
        tribeForm.value = {
            tribe_name: "",
            description: "",
            language: "",
            cultural_practices: "",
            category_id: "",
            population: "",
            region: "",
            image: null,
        };
    }
    showTribeModal.value = true;
};

const closeTribeModal = () => {
    showTribeModal.value = false;
    editingTribe.value = null;
};

const closeConfirmModal = () => {
    showConfirmModal.value = false;
    confirmModalConfig.value = {
        title: "",
        message: "",
    };
};

const confirmAction = () => {
    if (confirmModalConfig.value.onConfirm) {
        confirmModalConfig.value.onConfirm();
    }
    closeConfirmModal();
};

const showAlert = (message, type) => {
    showConfirmModal.value = true;
    confirmModalConfig.value = {
        title: type.charAt(0).toUpperCase() + type.slice(1),
        message: message,
        onConfirm: closeConfirmModal,
    };
};

const confirmDeleteTribe = (tribeId) => {
    showConfirmModal.value = true;
    confirmModalConfig.value = {
        title: "Delete Tribe",
        message: "Are you sure you want to delete this tribe?",
        onConfirm: () => {
            router.delete(route("staff.deleteTribe", { tribes: tribeId }), {
                onSuccess: () => {
                    tribes.value = tribes.value.filter(
                        (tribe) => tribe.id !== tribeId
                    );
                    showCreativeAlert.value = true;
                    creativeAlertConfig.value = {
                        action: "Deleted",
                        message:
                            "Your tribe has been removed from your collection.",
                    };
                },
                onError: (error) => {
                    console.error("Error deleting tribe:", error);
                    showAlert("Error: Unable to delete the tribe.", "error");
                },
            });
        },
    };
};

const confirmSubmitTribe = () => {
    submitTribe();
};

const submitTribe = () => {
    if (editingTribe.value) {
        // PUT request to update the tribe
        router.put(
            route("staff.updateTribe", { tribes: tribeForm.value.id }),
            tribeForm.value,
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    // Update the tribe in the tribes array directly
                    const tribeIndex = tribes.value.findIndex(
                        (r) => r.id === tribeForm.value.id
                    );
                    if (tribeIndex > -1) {
                        tribes.value[tribeIndex] = { ...tribeForm.value }; // Replace the old tribe with the updated one
                    }
                    closeTribeModal();
                    showCreativeAlert.value = true;
                    creativeAlertConfig.value = {
                        action: "Edited",
                        message:
                            "Your tribe has been updated from your collection.",
                    };
                },
                onError: (errors) => {
                    showAlert(
                        "There was an error updating the tribe: " +
                            Object.values(errors).join(", "),
                        "error"
                    );
                },
            }
        );
    } else {
        router.post(route("staff.storeTribe"), tribeForm.value, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                closeTribeModal();
                fetchDashboardData(); // Refresh the tribes list
                showCreativeAlert.value = true;
                creativeAlertConfig.value = {
                    action: "Added",
                    message:
                        "Your new tribe has been added to your collection.",
                };
            },
            onError: (errors) => {
                showAlert(
                    "There was an error adding the tribe: " +
                        Object.values(errors).join(", "),
                    "error"
                );
            },
        });
    }
};
const viewTribeDetails = (tribe) => {
    selectedTribe.value = tribe;
};

const closeTribeDetails = () => {
    selectedTribe.value = null;
};

const closeCreativeAlert = () => {
    showCreativeAlert.value = false;
};

const updateTribes = (newTribes) => {
    tribes.value = newTribes;
    recentTribes.value = newTribes.slice(0, 5);
};

const updateDashboardStats = () => {
    totalTribes.value = tribes.value.length;
    totalTribesCreated.value = tribes.value.length;
    totalCategories.value = categories.value.length;
};

const fetchDashboardData = async () => {
    try {
        const response = await fetch(route("staff.getDashboardStats"));
        const data = await response.json();
        totalTribes.value = data.totalTribes;
        totalTribesCreated.value = data.totalTribesCreated;
        totalCategories.value = data.totalCategories;
        recentTribes.value = data.recentTribes;
        categories.value = data.categories;
        tribes.value = data.tribes;
        updateDashboardStats();
    } catch (error) {
        console.error("Error fetching dashboard stats:", error);
        showAlert("Unable to fetch dashboard statistics.", "error");
    }
};

// Lifecycle hooks
onMounted(() => {
    fetchDashboardData();
});
</script>

<style scoped>
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #4f46e5 #e5e7eb;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #e5e7eb;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #4f46e5;
    border-radius: 4px;
}
</style>
